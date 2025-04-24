<?php

namespace App\Traits;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\{
    Http,
    Storage,
    DB
};
use Illuminate\Http\Response;
use App\Models\{
    User,
    Log,
    Metadata,
    Image,
    File,
    Inquiry,
    Page,
};
use Intervention\Image\Facades\Image as ImageInt;

trait GlobalTrait
{
    public function cypher ($request, $type) {
        $key = hex2bin('43e3b0f3405b2b7707e398f0171a91a2');
        $iv =  hex2bin('91bc845cbd4076fb9a0fdc2ad37e425d');
        $cypherMethod = 'AES-128-CBC';

        if ($type == 'decrypt') {
            $encrypted = $request->dt;
            $decrypted = openssl_decrypt($encrypted, $cypherMethod, $key, OPENSSL_ZERO_PADDING, $iv);
    
            $decrypted = trim($decrypted);
            $data = json_decode($decrypted);
        } else {
            $data = openssl_encrypt(json_encode($request), $cypherMethod, $key, 0, $iv);
        }

        return $data;
    }

    public function generateLog ($user, $action, $page = null, $item = null)
    {
        $authenticated = $this->getAuthenticatedUser($user);

        Log::create([
            // 'message' => "{$authenticated->userDetail->full_name} ({$authenticated->role->name}) {$message}"
            'editor_id' => $authenticated->id,
            'action' => $action,
            'page'  => $page,
            'item_name' => $item && $item->meta_title 
            ? 'Metadata' 
            : ($item->name ?? $item->title ?? null),
            'item_id' => $item->id ?? null,
        ]);
    }

    public function getAuthenticatedUser ($user)
    {
        if ($user) {
            $user = User::where('id', $user->id)
            ->with([
                'userDetail' => function ($query) {
                    $query->select('id', 'user_id', 'first_name', 'last_name', 'full_name', 'slug', 'contact_number');
                },
                'role' => function ($query) {
                    $query->select('id', 'identifier', 'permissions', 'name', 'type');
                }
            ])
            ->first();
        }
        return $user;
    }

    public function metatags ($record, $request)
    {
        $metadata = Metadata::where('parent_id', $record->id)->first();
        if (!$metadata) {
            Metadata::create([
                'parent_id' => $record->id,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'link_rel' => $request->link_rel
            ]);
        } else {
            $metadata->update([
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'link_rel' => $request->link_rel
            ]);
        }
    }

    public function recordExist ($record): Response
    {
        if ($record) {
            return response([
                'record' => $record
            ]);
        } else {
            return response([
                'errors' => [
                    'Not Found.'
                ]
            ]);
        }
    }

    public function slugify ($str, $model, $ref_id = null) {
        $MODEL = '\App\Models\\' . $model;
        $slug = str_slug( strtolower( $str ) , '-');

        $record =  $MODEL::whereSlug( $slug )->first();
        if ( !is_null( $record ) ) {
            // $query  = $MODEL::where('slug','like', $slug.'%')->whereNull('deleted_at');
            $query  = $MODEL::where('slug', 'LIKE', $slug.'%')->withTrashed();

            if (!is_null($ref_id)){
                $query = $query->where('id','!=',$ref_id);
            }

            $count  = $query->latest('id')->count();
            if ($count > 0) {
                $count++;
                $slug = "{$slug}-{$count}";
            }
        }

        return $slug;
    }

    /**
     * [addImages function]
     * @param [string] $model - Name of the model in snake case. Make it singular (e.g. product_variant, customer_detail)
     * @param [object/array] $r - The Request object. It contains the images
     * @param [Table record] $record - Model record
     * @param [String] $field  - string, name of the file field
     */
    function addImages ($model, $r, $record, $field = NULL, $type = NULL) {
        $file = ($field) ?? 'file';
        $existingImagesCount = $record->images($model)->count();

        $file_id  = $file . '_id';
        $file_title  = $file . '_title';
        $file_alt = $file . '_alt';
        $file_category = $file . '_category';
        $file_sequence = $file . '_sequence';
        $file_caption = $file . '_caption';
        $file_link = $file . '_link';
        foreach ($r->$file as $key => $image) {
            
    // \Log::info($r->$file);
            $proceed = true;

            if ($proceed) {
                $uploadedImage = $this->uploadFile($image, null, null, $model);
                $imageData = [
                    'model' => $model,
                    'title' => (isset($r->$file_title)) ? $r->$file_title[$key] : null,
                    'alt' => (isset($r->$file_alt)) ? $r->$file_alt[$key] : null,
                    'path' => $uploadedImage->path,
                    'path_resized' => $uploadedImage->path_resized,
                    'category' => (isset($r->$file_category)) ? $r->$file_category[$key] : $field,
                    'sequence' => (isset($r->$file_sequence)) ? $r->$file_sequence[$key] : 0, //$existingImagesCount + 1,
                    'name' => $uploadedImage->original_file_name,
                    'size' => $uploadedImage->file_size,
                    'caption'   => (isset($r->$file_caption)) ? $r->$file_caption[$key] : null,
                    'link'   => (isset($r->$file_link)) ? $r->$file_link[$key] : null,
                ];

                $type === 'file' ? $record->uploadPageFile($imageData) : $record->uploadImage($imageData);
            }
        }
    }

    function updateImages ($model, $r, $record, $field = NULL, $type = NULL) {
        $file = ($field) ?? 'file';
        $existingImagesCount = $record->images($model)->count();

        $file_id  = $file . '_id';
        $file_title  = $file . '_title';
        $file_alt = $file . '_alt';
        $file_category = $file . '_category';
        $file_sequence = $file . '_sequence';
        $file_caption = $file . '_caption';
        $file_link = $file . '_link';

        if ($r->$file_id) {
            // $file = ($field) ?? 'file';
            
            foreach ($r->$file_id as $key => $image_id) {
                if (!$image_id && !empty($r->$file)) { # if new image, upload this
                    // dd($key);
                    $image = $r->$file[count($r->$file_id) - count($r->$file) <= 0 ? $key : ($key - (count($r->$file_id) - count($r->$file)))];
                    $uploadedImage = $this->uploadFile($image, null, null, $model);
                    $imageData = [
                        'title' => (isset($r->$file_title)) ? $r->$file_title[$key] : null,
                        'alt' => (isset($r->$file_alt)) ? $r->$file_alt[$key] : null,
                        'sequence' => (isset($r->$file_sequence)) ? $r->$file_sequence[$key] : 0, //$existingImagesCount + 1,
                        'path' => $uploadedImage->path,
                        'path_resized' => $uploadedImage->path_resized,
                        'category' => (isset($r->$file_category)) ? $r->$file_category[$key] : null,
                        'model' => $model,
                        'name' => $uploadedImage->original_file_name,
                        'size' => $uploadedImage->file_size,
                        'caption'   => (isset($r->$file_caption)) ? $r->$file_caption[$key] : null,
                        'link'   => (isset($r->$file_link)) ? $r->$file_link[$key] : null
                    ];
                    
                    $type === 'file' ? $record->uploadPageFile($imageData) : $record->uploadImage($imageData);

                } else { # if old image
                    // dd($key);
                    $_IMAGE_Model = '\App\Models\\Image';
                    // if (isset($r->$file[$key])) { # if a new image is selected
                    //     # update the old image data
                    //     $existingImage = $_IMAGE_Model::where('id', $image_id)->first();
                    //     $uploadedImage = $this->uploadFile($r->$file[$key], $existingImage->path, $existingImage->path_resized, $model);
                    //     $existingImage->update([
                    //         'title' => (isset($r->$file_title)) ? $r->$file_title[$key] : null,
                    //         'alt' => (isset($r->$file_alt)) ? $r->$file_alt[$key] : null,
                    //         'sequence' => (isset($r->$file_sequence)) ? $r->$file_sequence[$key] : 0, //$existingImagesCount + 1,
                    //         'path' => $uploadedImage->path,
                    //         'path_resized' => $uploadedImage->path_resized,
                    //         'name' => $uploadedImage->original_file_name,
                    //         'size' => $uploadedImage->file_size,
                    //         'caption'   => (isset($r->$file_caption)) ? $r->$file_caption[$key] : null,
                    //         'link'   => (isset($r->$file_link)) ? $r->$file_link[$key] : null
                    //     ]);
                    // } else { # if no new image is selected
                        $existingImage = $_IMAGE_Model::where('id', $image_id)->first();
                        $existingImage->update([
                            'title' => (isset($r->$file_title)) ? $r->$file_title[$key] : null,
                            'alt' => (isset($r->$file_alt)) ? $r->$file_alt[$key] : null,
                            'sequence' => (isset($r->$file_sequence)) ? $r->$file_sequence[$key] : 0, //$existingImagesCount + 1,
                            'caption'   => (isset($r->$file_caption)) ? $r->$file_caption[$key] : null,
                            'link'   => (isset($r->$file_link)) ? $r->$file_link[$key] : null
                        ]);
                    // }
                }
            }
        } else  if (!empty($r->$file)){
            foreach ($r->$file as $key => $image_id) {
                // \Log::info( "test");
                $image = $r->$file[$key];
                $uploadedImage = $this->uploadFile($image, null, null, $model);
                $imageData = [
                    'title' => (isset($r->$file_title[$key])) ? $r->$file_title[$key] : null,
                    'alt' => (isset($r->$file_alt[$key])) ? $r->$file_alt[$key] : null,
                    'sequence' => (isset($r->$file_sequence[$key])) ? $r->$file_sequence[$key] : 0, //$existingImagesCount + 1,
                    'path' => $uploadedImage->path,
                    'path_resized' => $uploadedImage->path_resized,
                    'category' => (isset($r->$file_category[$key])) ? $r->$file_category[$key] : null,
                    'model' => $model,
                    'name' => $uploadedImage->original_file_name,
                    'size' => $uploadedImage->file_size,
                    'caption'   => (isset($r->$file_caption[$key])) ? $r->$file_caption[$key] : null,
                    'link'   => (isset($r->$file_link[$key])) ? $r->$file_link[$key] : null
                ];
                
                $type === 'file' ? $record->uploadPageFile($imageData) : $record->uploadImage($imageData);
            }
        }
    } 

    function imageUploader ($model, $r, $record, $field = NULL, $action, $type = NULL)
    {
        $imageField          = ($field) ?? 'image';
        $existingImagesCount = $type === 'file' ? $record->files($model)->count() : $record->images($model)->count();

        $imageFieldId        = $imageField . '_id';
        $imageFieldIdOld     = $imageField . '_id_old';
        $imageFieldTitle     = $imageField . '_title';
        $imageFieldAlt       = $imageField . '_alt';
        $imageFieldCategory  = $imageField . '_category';
        $imageFieldSequence  = $imageField . '_sequence';
        $imageFieldCaption   = $imageField . '_caption';
        $imageFieldExisting  = $imageField . '_existing';
        $imageFieldMultiple  = $imageField . '_upload_multiple';

        switch ($action) {
            case 'add':
                foreach ($r->$imageFieldId as $key => $id) {
                    $uploadedImage = null;

                    if (!$r->$imageFieldId[$key] && $r->$imageFieldExisting[$key]) {
                        $uploadedImage = $type === 'file' ? File::where('id', $r->$imageFieldExisting[$key])->first() : Image::where('id', $r->$imageFieldExisting[$key])->first();
                    } elseif (!$r->$imageFieldId[$key] && !$r->$imageFieldExisting[$key]) {
                        $uploadedImage = $this->uploadFile($r->$imageField[$key], null, null, $model);
                    }

                    if ($uploadedImage) {
                        $payload = [
                            'model'        => $model,
                            'title'        => (isset($r->$imageFieldTitle)) ? $r->$imageFieldTitle[$key] : null,
                            'alt'          => (isset($r->$imageFieldAlt)) ? $r->$imageFieldAlt[$key] : null,
                            'path'         => (isset($r->$imageField[$key])) ? $uploadedImage->path : $uploadedImage->original_path,
                            'path_resized' => (isset($r->$imageField[$key])) ? $uploadedImage->path_resized : $uploadedImage->original_path_resized,
                            'category'     => (isset($r->$imageFieldCategory)) ? $r->$imageFieldCategory[$key] : $field,
                            'sequence'     => (isset($r->$imageFieldSequence)) ? $r->$imageFieldSequence[$key] : 0,
                            'name'         => $uploadedImage->original_file_name,
                            'size'         => $uploadedImage->file_size,
                            'caption'      => (isset($r->$imageFieldCaption)) ? $r->$imageFieldCaption[$key] : null
                        ];

                        $type === 'file' ? $record->uploadPageFile($payload) : $record->uploadImage($payload);
                    }
                }
                break;
            case 'update':
                if (isset($r->$imageFieldId)) {
                    foreach ($r->$imageFieldId as $key => $id) {
                        $uploadedImage = null;
                        $new = false;
    
                        if (!$r->$imageFieldId[$key] && $r->$imageFieldExisting[$key]) {
                            $uploadedImage = $type === 'file' ? File::where('id', $r->$imageFieldExisting[$key])->first() : Image::where('id', $r->$imageFieldExisting[$key])->first();
                            $new = true;
                        } elseif ($r->$imageFieldId[$key] && $r->$imageFieldExisting[$key]) {
                            $uploadedImage = $type === 'file' ? File::where('id', $r->$imageFieldExisting[$key])->first() : Image::where('id', $r->$imageFieldExisting[$key])->first();
                        } elseif (!$r->$imageFieldId[$key] && !$r->$imageFieldExisting[$key]) {
                            if (isset($r->$imageField[$key])) {
                                $uploadedImage = $this->uploadFile($r->$imageField[$key], null, null, $model);
                                $new = true;
                            }
                        }
                        
                        if ($uploadedImage && isset($r->$imageFieldMultiple[$key])) {
                            if (!!$r->$imageFieldMultiple[$key]) {
                                if ($new) {
                                    $payload = [
                                        'model'        => $model,
                                        'title'        => (isset($r->$imageFieldTitle)) ? $r->$imageFieldTitle[$key] : null,
                                        'alt'          => (isset($r->$imageFieldAlt)) ? $r->$imageFieldAlt[$key] : null,
                                        'path'         => (isset($r->$imageField[$key])) ? $uploadedImage->path : $uploadedImage->original_path,
                                        'path_resized' => (isset($r->$imageField[$key])) ? $uploadedImage->path_resized : $uploadedImage->original_path_resized,
                                        'category'     => (isset($r->$imageFieldCategory)) ? $r->$imageFieldCategory[$key] : $field,
                                        'sequence'     => (isset($r->$imageFieldSequence)) ? $r->$imageFieldSequence[$key] : 0,
                                        'name'         => (isset($r->$imageField[$key])) ? $uploadedImage->original_file_name : $uploadedImage->name,
                                        'size'         => (isset($r->$imageField[$key])) ? $uploadedImage->file_size : $uploadedImage->size,
                                        'caption'      => (isset($r->$imageFieldCaption)) ? $r->$imageFieldCaption[$key] : null
                                    ];
                                    
                                    $type === 'file' ? $record->uploadPageFile($payload) : $record->uploadImage($payload);
                                } else {
                                    $uploadedImage->update([
                                        'title'        => (isset($r->$imageFieldTitle)) ? $r->$imageFieldTitle[$key] : null,
                                        'alt'          => (isset($r->$imageFieldAlt)) ? $r->$imageFieldAlt[$key] : null,
                                        'sequence'     => (isset($r->$imageFieldSequence)) ? $r->$imageFieldSequence[$key] : 0,
                                        'caption'      => (isset($r->$imageFieldCaption)) ? $r->$imageFieldCaption[$key] : null
                                    ]);
                                }
                            } else {
                                if ($new) {
                                    if (isset($r->$imageFieldIdOld[$key])) {
                                        $existingImage = $file === 'file' ? File::where('id', $r->$imageFieldIdOld[$key])->first() : Image::where('id', $r->$imageFieldIdOld[$key])->first();
        
                                        $existingImage->update([
                                            'title'        => (isset($r->$imageFieldTitle)) ? $r->$imageFieldTitle[$key] : null,
                                            'alt'          => (isset($r->$imageFieldAlt)) ? $r->$imageFieldAlt[$key] : null,
                                            'path'         => (isset($r->$imageField[$key])) ? $uploadedImage->path : $uploadedImage->original_path,
                                            'path_resized' => (isset($r->$imageField[$key])) ? $uploadedImage->path_resized : $uploadedImage->original_path_resized,
                                            'sequence'     => (isset($r->$imageFieldSequence)) ? $r->$imageFieldSequence[$key] : 0,
                                            'name'         => (isset($r->$imageField[$key])) ? $uploadedImage->original_file_name : $uploadedImage->name,
                                            'size'         => (isset($r->$imageField[$key])) ? $uploadedImage->file_size : $uploadedImage->size,
                                            'caption'      => (isset($r->$imageFieldCaption)) ? $r->$imageFieldCaption[$key] : null
                                        ]);
                                    } else {
                                        $payload = [
                                            'model'        => $model,
                                            'title'        => (isset($r->$imageFieldTitle)) ? $r->$imageFieldTitle[$key] : null,
                                            'alt'          => (isset($r->$imageFieldAlt)) ? $r->$imageFieldAlt[$key] : null,
                                            'path'         => (isset($r->$imageField[$key])) ? $uploadedImage->path : $uploadedImage->original_path,
                                            'path_resized' => (isset($r->$imageField[$key])) ? $uploadedImage->path_resized : $uploadedImage->original_path_resized,
                                            'category'     => (isset($r->$imageFieldCategory)) ? $r->$imageFieldCategory[$key] : $field,
                                            'sequence'     => (isset($r->$imageFieldSequence)) ? $r->$imageFieldSequence[$key] : 0,
                                            'name'         => (isset($r->$imageField[$key])) ? $uploadedImage->original_file_name : $uploadedImage->name,
                                            'size'         => (isset($r->$imageField[$key])) ? $uploadedImage->file_size : $uploadedImage->size,
                                            'caption'      => (isset($r->$imageFieldCaption)) ? $r->$imageFieldCaption[$key] : null
                                        ];
                                        
                                        $type === 'file' ? $record->uploadPageFile($payload) : $record->uploadImage($payload);
                                    }
                                } else {
                                    $uploadedImage->update([
                                        'title'        => (isset($r->$imageFieldTitle)) ? $r->$imageFieldTitle[$key] : null,
                                        'alt'          => (isset($r->$imageFieldAlt)) ? $r->$imageFieldAlt[$key] : null,
                                        'sequence'     => (isset($r->$imageFieldSequence)) ? $r->$imageFieldSequence[$key] : 0,
                                        'caption'      => (isset($r->$imageFieldCaption)) ? $r->$imageFieldCaption[$key] : null
                                    ]);
                                }
                            }
                        }
                    }
                }
                break;
        }
    }

    function getContentType ($extension) {
        $result;
        switch ($extension) {
            case 'jpg':
            case 'JPG':
                $result = 'image/jpg';
                break;
            case 'jpeg':
            case 'JPEG':
                $result = 'image/jpeg';
                break;
            case 'png':
            case 'PNG':
                $result = 'image/png';
                break;
            case 'svg':
            case 'SVG':
                $result = 'image/svg+xml';
                break;
            case 'gif':
            case 'GIF':
                $result = 'image/gif';
                break;
            case 'pdf':
            case 'PDF':
                $result = 'application/pdf';
                break;
            case 'ppt':
            case 'PPT':
                $result = 'application/vnd.ms-powerpoint';
                break;
            case 'pptx':
            case 'PPTX':
                $result = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
                break;
            case 'xls':
            case 'XLS':
                $result = 'application/vnd.ms-excel';
                break;
            case 'xlsx':
            case 'XLSX':
                $result = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case 'docx':
            case 'DOCX':
                $result = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                break;
            case 'doc':
            case 'DOC':
                $result = 'application/msword';
                break;
            case 'txt':
            case 'TXT':
                $result = 'text/plain';
                break;
            case 'mp4':
            case 'MP4':
                $result = 'video/mp4';
                break;
            case 'webm':
            case 'WEBM':
                $result = 'video/webm';
                break;
        }

        return $result;
    }

    function deleteFile ($oldFilePath, $oldFilePathResized)
    {
        // $disk = 'public'; # s3 kapag s3. public kapag sa local lang isesave
        $disk = 'public'; # s3 kapag s3. public kapag sa local lang isesave

        # delete the old file if it exists
        if ($oldFilePath != null) {
            Storage::disk($disk)->delete("$oldFilePath");
        }
        if ($oldFilePathResized != null) {
            Storage::disk($disk)->delete("$oldFilePathResized");
        }
    }

    function checkExternalFile ($url)
    {
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL, $url);
        // don't download content
        curl_setopt($channel, CURLOPT_NOBODY, 1);
        curl_setopt($channel, CURLOPT_FAILONERROR, 1);
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($channel);
        curl_close($channel);
        if ($result !== FALSE) {
            return true;
        } else {
            return false;
        }
    }

    function uploadExternalFile ($url, $oldFilePath = null, $oldFilePathResized = null, $model = null)
    {
        // $disk = 'public'; # s3 kapag s3. public kapag sa local lang isesave
        $disk = 'public'; # s3 kapag s3. public kapag sa local lang isesave

        # delete the old file if it exists
        if ($oldFilePath != null) {
            Storage::disk($disk)->delete("uploads/$oldFilePath");
        }
        if ($oldFilePathResized != null) {
            Storage::disk($disk)->delete("uploads/$oldFilePathResized");
        }

        $fileInfo = pathinfo($url);
        $filename = str_replace(array('’'), '-', $fileInfo['filename']);

        $extension = $fileInfo['extension'];
        $headers = get_headers($url, 1);
        $size = $headers["Content-Length"];
        
        $folderDate = Carbon::now()->format('Y-m-d');
        $folderTime = Carbon::now()->format('H-i-s-u');
        $filenameToStoreOriginal = $filename . '.' . $extension;
        $filenameToStore = $folderDate . '_' . $folderTime . '.' . $extension;

        $uploadPath = "uploads/". $model ."/$folderDate/$filenameToStore";

        $unresizedFile = ImageInt::make($url)
        ->interlace()
        ->encode($extension, 80)
        ->orientate();

        Storage::disk($disk)->put($uploadPath, $unresizedFile->getEncoded(), 'public');

        # get the file size
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        $fileSize = number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];

        # upload resized file
        $resizedFile = ImageInt::make($url)->resize(750, 750, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        })
        ->interlace()
        ->encode($extension, 80)
        ->orientate();

        $filenameToStore = $folderDate . '_' . $folderTime . '_resized.' . $extension;

        $uploadPathResized = "uploads/" . $model. "/$folderDate/$filenameToStore";

        Storage::disk($disk)->put($uploadPathResized, $resizedFile->getEncoded(), 'public');

        $record = (object) [
            'path' => $uploadPath,
            'path_resized' => $uploadPathResized,
            'original_file_name' => $filename,
            'main_original_file_name' =>  $filenameToStoreOriginal,
            'file_size' => $fileSize,
            'file_type' => $extension
        ];

        return $record;
    }

    function uploadFile ($file, $oldFilePath = null, $oldFilePathResized = null, $model = null) {
        // $disk = 'public'; # s3 kapag s3. public kapag sa local lang isesave
        $disk = 'public'; # s3 kapag s3. public kapag sa local lang isesave

        # get the file size
        $size = filesize($file);
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        $fileSize = number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];

        # delete the old file if it exists
        if ($oldFilePath != null) {
            Storage::disk($disk)->delete("uploads/$oldFilePath");
        }
        if ($oldFilePathResized != null) {
            Storage::disk($disk)->delete("uploads/$oldFilePathResized");
        }

        $filenameWithExtension = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
        $folderDate = Carbon::now()->format('Y-m-d');
        $folderTime = Carbon::now()->format('H-i-s-u');
        $filenameToStoreOriginal = $filename . '.' . $extension;
        $filenameToStore = str_slug( $filename , '-') . '_' . $folderTime . '.' . $extension;
        $otherAcceptedExtensions = ['mp4', 'webm', 'svg', 'gif', 'pdf', 'ppt', 'pptx', 'xls', 'xlsx', 'docx', 'doc', 'txt','MP4', 'WEBM', 'SVG', 'GIF', 'PDF', 'PPT', 'PPTX', 'XLS', 'XLSX', 'DOCX', 'DOC', 'TXT'];

        // $uploadPath = "uploads/$folderDate/$folderTime/$filenameToStore"; //old
        if (is_null($model)){
            $uploadPath = "uploads/$folderDate/$filenameToStore";
        }else{
            $uploadPath = "uploads/". $model ."/$folderDate/$filenameToStore";
        }

        # if the file is svg or gif, directly upload it and stop the function immediately by returning the path names
        if (in_array($extension, $otherAcceptedExtensions)) {
            Storage::disk($disk)->put($uploadPath, file_get_contents($file), [
                'visibility' => 'public',
                'ContentType' => $this->getContentType($extension)
            ]);

            $toReturn = (object) [
                'path' => $uploadPath,
                'path_resized' => $uploadPath,
                'original_file_name' => $filenameToStore,
                'file_size' => $fileSize,
                'file_type' => $extension,
                'main_original_file_name' =>  $filenameToStoreOriginal,
            ];

            return $toReturn;
        }

        // $filenameToStore_resized = $filename . '_thumbnail.' . $extension;
        $filenameToStore_resized = str_slug( $filename , '-') . '_' . $folderTime . '_resized.' . $extension;
        // $uploadPathResized = "uploads/$folderDate/$folderTime/$filenameToStore_resized"; //old
        if (is_null($model)){
            $uploadPathResized = "uploads/all_files/$folderDate/$filenameToStore_resized";
        } else {
            $uploadPathResized = "uploads/" . $model. "/$folderDate/$filenameToStore_resized";
        }

        # check if image extension is png if png straight upload using Storage function and not use the image intervention
        if (strtolower($extension) == 'png') {
            # main image
            Storage::disk($disk)->put($uploadPath, file_get_contents($file), [
                'visibility' => 'public',
                'ContentType' => $this->getContentType($extension)
            ]);
            
            #resize image
            Storage::disk($disk)->put($uploadPathResized, file_get_contents($file), [
                'visibility' => 'public',
                'ContentType' => $this->getContentType($extension)
            ]);

        }
        else {
            $unresizedFile = ImageInt::make($file->getRealPath())->interlace()->encode($extension, 80)->orientate();
            Storage::disk($disk)->put($uploadPath, $unresizedFile->getEncoded(), 'public');
    
            # upload resized file
            $resizedFile = ImageInt::make($file->getRealPath())->resize(750, 750, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            })->interlace()->encode($extension, 80)
            ->orientate();
            Storage::disk($disk)->put($uploadPathResized, $resizedFile->getEncoded(), 'public');
        }

        $toReturn = (object) [
            // 'path' => "uploads/$folderDate/$folderTime/$filenameToStore",
            // 'path_resized' => "uploads/$folderDate/$folderTime/$filenameToStore_resized",
            'path' => $uploadPath,
            'path_resized' => $uploadPathResized,
            'original_file_name' => $filenameWithExtension,
            'main_original_file_name' =>  $filenameToStoreOriginal,
            'file_size' => $fileSize,
            'file_type' => $extension
        ];

        // make a copy of images uploaded, if webp convert to png | if png/jpg/jpeg converto to webp
        if ($disk == 's3') {
            $original_path = config('app.aws_url')."$uploadPath";
            $original_path_resized = config('app.aws_url')."$uploadPathResized";
        } else {
            $original_path = config('app.api_url')."/storage/$uploadPath";
            $original_path_resized = config('app.api_url')."/storage/$uploadPathResized";
        }
        $this->copyImage(0, $uploadPath, $original_path, $disk);
        $this->copyImage(0, $uploadPathResized, $original_path_resized, $disk);

        return $toReturn;
    }

    function fileIsImage ($file) {
        $result = false;
        if (@is_array(getimagesize($file))){
            $result = true;
        }

        return $result;
    }

    //embed google maps
    function extractMapCode($iframeCode)
    {
    // Use regular expressions to extract the specific part from the src attribute
    preg_match('/\?pb=(.*?)"/', $iframeCode, $matches);

    if (isset($matches[1])) {
        return $matches[1];
    }
    return null; // Return null if the specific part is not found
    
    }
    function validateYoutubeLink ($link) {
        $youtubeId = '';
        try {
            // Match the YouTube video ID pattern
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $link, $match)) {
                $youtubeId = $match[1];
            } else {
                // Return invalid if no match found
                return (object) [
                    'valid' => false,
                    'thumbnail' => null
                ];
            }
        } catch (Exception $e) {
            return (object) [
                'valid' => false,
                'thumbnail' => null
            ];
        }
    
        $response = Http::get("https://www.googleapis.com/youtube/v3/videos?part=snippet&id=$youtubeId&key=AIzaSyAHYpOW2iaCEyn2jMgplCszWEMH09SE7xk");
    
        $result = $response->json()['pageInfo']['totalResults'];

        if ($result == 1) {
            if (isset($response->json()['items'][0]['snippet']['thumbnails']['maxres'])) {
                $thumbnail = $response->json()['items'][0]['snippet']['thumbnails']['maxres']['url'];
            } else if (isset($response->json()['items'][0]['snippet']['thumbnails']['standard'])) {
                $thumbnail = $response->json()['items'][0]['snippet']['thumbnails']['standard']['url'];
            } else if (isset($response->json()['items'][0]['snippet']['thumbnails']['high'])) {
                $thumbnail = $response->json()['items'][0]['snippet']['thumbnails']['high']['url'];
            } else if (isset($response->json()['items'][0]['snippet']['thumbnails']['medium'])) {
                $thumbnail = $response->json()['items'][0]['snippet']['thumbnails']['medium']['url'];
            } else {
                $thumbnail = $response->json()['items'][0]['snippet']['thumbnails']['default']['url'];
            }

            $title = $response->json()['items'][0]['snippet']['title'];
            $description = $response->json()['items'][0]['snippet']['description'];
            $published = $response->json()['items'][0]['snippet']['publishedAt'];
            $embedUrl = "https://www.youtube.com/embed/$youtubeId";
    
            return (object) [
                'valid' => true,
                'youtubeId' => $youtubeId,
                'thumbnail' => $thumbnail,
                'title' => $title,
                'description' => $description,
                'published' => $published,
                'embedUrl' => $embedUrl,
            ];
        } else {
            return (object) [
                'valid' => false,
                'youtubeId' => null,
                'thumbnail' => null,
                'title' => null,
                'description' => null,
                'published' => null,
                'embedUrl' => null,
            ];
        }
    }

    /**
     * [copyImage function]
     * @param [integer] $id - Id of image to copy
     * @param [String] $image  - image path to copy
     * @param [String] $original_path  - image path to copy
     * @param [String] $disk  - disk
     */
    public function copyImage($id, $image, $original_path, $disk) {
        $path = [];
        $image_path = $image;
        $new_path = dirname($image);
        $file_name = pathinfo($image, PATHINFO_FILENAME);
        $file_extension = pathinfo($image, PATHINFO_EXTENSION);
        $new_file_path = '';
        $extension = '';
        // check if image path is existing in drive
        if (strtolower($file_extension) != 'svg') {
            $exist = Storage::disk($disk)->exists($image_path);
            if ($exist) {
                // check if image is png/jpg/jpeg/webp
                switch (strtolower($file_extension)) {
                    case 'png':
                    case 'jpg':
                    case 'jpeg':
                        $new_file_path = $new_path.'/'.$file_name.".webp";
                        $extension = 'webp';
                        break;
                    case 'webp':
                        $new_file_path = $new_path.'/'.$file_name.".jpg";
                        $extension = 'jpg';
                        break;
                }

                // check new file path is empty if empty the image is not png/jpg/jpeg/webp
                if ($new_file_path != '') {
                    $new_file_exist = Storage::disk($disk)->exists($new_file_path);
                    // check if new file is existing
                    if ($new_file_exist) {
                        $path = array(
                            'id' => $id,
                            'image_path' => $image_path,
                            'new_file_path' => $new_file_path,
                            'message' => 'no need to copy'
                        );
                    }
                    else {
                        // upload resized file
                        $uploaded = ImageInt::make($original_path)
                        ->interlace()
                        ->encode($extension, 80)
                        ->orientate();

                        $copied = Storage::disk($disk)->put($new_file_path, $uploaded->getEncoded());
                        
                        // check if copied successfully
                        if ((!$copied)) {
                            $path = array(
                                'id' => $id,
                                'image_path' => $image_path,
                                'new_file_path' => $new_file_path,
                                'message' => 'not copied'
                            );
                        }
                        else{ 
                            $path = array(
                                'id' => $id,
                                'image_path' => $image_path,
                                'new_file_path' => $new_file_path,
                                'message' => 'copied'
                            );
                        }
                    }
                }
                else {
                    $path = array(
                        'id' => $id,
                        'image_path' => $image_path,
                        'new_file_path' => $new_file_path,
                        'message' => 'no need to copy'
                    );
                }
            }
            else {
                $path = array(
                    'id' => $id,
                    'image_path' => $image_path,
                    'new_file_path' => $new_file_path,
                    'message' => 'not exist'
                );
            }
        }
        else {
            $path = array(
                'id' => $id,
                'image_path' => $image_path,
                'new_file_path' => $new_file_path,
                'message' => 'no need to copy'
            );
        }

        return $path;
    }

    /**
     * [filenameChange function]
     * @param [Object] $data
     */
    public function filenameChange($data) {
        $image = Image::where('id', $data->id)->first();
        if ($image) {
            $rename = false;
            $renameCopy = false;
            $folderTime = Carbon::create($image->updated_at)->format('H-i-s-u');
            $originalFilename = $image->name;
            $path = dirname($image->filename);

            $filename = pathinfo($originalFilename, PATHINFO_FILENAME);
            $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
            $extensionCopy = '';
            
            if (strtolower($extension) != 'svg') {
                switch (strtolower($extension)) {
                    case 'png':
                    case 'jpg':
                    case 'jpeg':
                        $extensionCopy = 'webp';
                        break;
                    case 'webp':
                        $extensionCopy = 'jpg';
                        break;
                }
            }
            
            $newFilename = str_slug( $filename , '-') . '_' . $folderTime . '.' . $extension;
            $newFilenameResized = str_slug( $filename , '-') . '_' . $folderTime . '_resized.' . $extension;
            $newPath = "$path/$newFilename";
            $newPathResized = "$path/$newFilenameResized";

            if (Storage::disk('public')->exists($image->filename) && Storage::disk('public')->exists($image->fileresized)) {
                if ($extensionCopy != '') {
                    $convertedFilename = pathinfo($image->filename, PATHINFO_FILENAME);
                    $convertedFilenameResized = pathinfo($image->fileresized, PATHINFO_FILENAME);
                    $oldCopyPath = "$path/$convertedFilename.$extensionCopy";
                    $oldCopyPathResized = "$path/$convertedFilenameResized.$extensionCopy";

                    $newCopyFilename = str_slug( $filename , '-') . '_' . $folderTime . '.' . $extensionCopy;
                    $newCopyFilenameResized = str_slug( $filename , '-') . '_' . $folderTime . '_resized.' . $extensionCopy;
                    $newCopyPath = "$path/$newCopyFilename";
                    $newCopyPathResized = "$path/$newCopyFilenameResized";
                    if (Storage::disk('public')->exists($oldCopyPath) && Storage::disk('public')->exists($oldCopyPathResized)) { 
                        rename(getcwd()."/storage/$oldCopyPath", getcwd()."/storage/$newCopyPath");
                        rename(getcwd()."/storage/$oldCopyPathResized", getcwd()."/storage/$newCopyPathResized");
                        $renameCopy = true;
                    }
                }
                rename(getcwd()."/storage/$image->filename", getcwd()."/storage/$newPath");
                if ($extension != 'svg') {
                    rename(getcwd()."/storage/$image->fileresized", getcwd()."/storage/$newPathResized");
                }
                $rename = true;
                $image->update([
                    'path' => $newPath,
                    'path_resized' => $newPathResized
                ]);
            }

            return (object) [
                'success' => $rename,
                'successCopy' => $renameCopy,
                'image' => (object) [
                    'id' => $image->id,
                    'name' => $image->name,
                    'path' => $image->filename,
                    'path_resized' => $image->fileresized,
                    'model' => $image->model,
                    'category' => $image->category,
                ],
            ];
        }
    }

    
    public function reassignOrderValues(string $model, string $parent = null, string $job_type = null)
    {
        $modelClass = "App\\Models\\$model";
        $order = 1;

        $records = $modelClass::orderBy('order') // Ensure ordered processing
            ->when(isset($parent), function ($query) use ($parent) {
                $query->where('parent_id', $parent);
            })
            ->when(isset($job_type), function ($query) use ($job_type) {
                $query->where('job_type', $job_type);
            })->get();

        foreach ($records as $index => $record) {
            $record->update(['order' => $index + 1]);
        }
            // ->chunkById(100, function ($items) use (&$order) {
            //     foreach ($items as $item) {
            //         $item->update(['order' => $order++]);
            //     }
            // });
    }


    function generateGmapsEmbedCode(string $shareLink): ?string
    {
        // Resolve the final URL from the share link
        $resolvedUrl = $this->resolveRedirectUrl($shareLink);

        // Ensure the URL is a valid Google Maps URL
        if (!str_contains($resolvedUrl, 'maps.google.com/maps')) {
            return null;
        }

        // Parse the resolved URL
        $parsedUrl = parse_url($resolvedUrl);
        $path = $parsedUrl['path'] ?? '';
        $query = $parsedUrl['query'] ?? '';
        parse_str($query, $queryParams);

        // Try to extract coordinates or place ID
        $coords = null;

        // Check if coordinates are available in the URL path
        if (preg_match('/\/@([^\/]+)\//', $path, $matches)) {
            $coords = explode(',', $matches[1]);
        }

        // If coordinates aren't found, check query parameters
        if (!$coords && isset($queryParams['q'])) {
            $coords = explode(',', $queryParams['q']);
        }

        // If coordinates are valid, construct the embed URL
        if ($coords && count($coords) == 2) {
            $lat = $coords[0];
            $lng = $coords[1];
            $embedUrl = "https://www.google.com/maps/embed?pb=YOUR_EMBED_CODE_HERE&q=$lat,$lng&z=15&output=embed";

            return "<iframe width='600' height='450' style='border:0;' src='$embedUrl' allowfullscreen loading='lazy'></iframe>";
        }

        return null;
    }

    function resolveRedirectUrl(string $url): string
    {
        $client = new Client(['allow_redirects' => true]);
    
        try {
            $response = $client->request('GET', $url, [
                'http_errors' => false,
                'allow_redirects' => true,
            ]);
    
            // Extract final URL from redirects or body content
            $effectiveUrl = $response->getHeader('X-Guzzle-Redirect-History');
            if ($effectiveUrl) {
                return end($effectiveUrl); // Return the last URL in the redirect history
            }
            return (string) $response->getBody();
        } catch (\Exception $e) {
            return $url; // Fallback to the original URL
        }
    }

    function generateUniqueCode()
    {
        do {
            $part1 = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            $part2 = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            $part3 = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            
            $code = "$part1-$part2-$part3";
        } while (Inquiry::where('inquiry_number', $code)->exists()); 

        return $code;
    }

}