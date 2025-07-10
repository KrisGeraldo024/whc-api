<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use Illuminate\Support\Facades\{
    DB,
    Validator,
};
use App\Services\Taxonomy\TaxonomyService;
use Illuminate\Validation\Rule;
use App\Models\Taxonomy;
use App\Traits\GlobalTrait;

class TaxonomyController extends Controller
{


    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * @var TaxonomyService
     */
    protected $taxonomyService;

    /**
     * TaxonomyController constructor
     *
     * @param TaxonomyService $taxonomyService
     */
    public function __construct(TaxonomyService $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * Display a listing of unit types with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showPropertyStatuses(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $propertyStatuses = $this->taxonomyService->getPropertyStatuses($sortBy, $sortDirection, $request->filled('all'));
        return response($propertyStatuses)->setStatusCode(200);
    }


    /**
     * Display a listing of brands with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showBrand(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $brand = $this->taxonomyService->getBrand($sortBy, $sortDirection, $request->filled('all'), $request->propertyType);
        return response($brand)->setStatusCode(200);
    }

    public function showMaterial(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $material = $this->taxonomyService->getMaterial($sortBy, $sortDirection, $request->filled('all'), $request->propertyType);
        return response($material)->setStatusCode(200);
    }

    /**
     * Display a listing of types with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showPurpose(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $purpose = $this->taxonomyService->getPurpose($sortBy, $sortDirection, $request->filled('all'), $request->propertyType);
        return response($purpose)->setStatusCode(200);
    }

    /**
     * Display a listing of finish with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showFinish(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $finish = $this->taxonomyService->getFinish($sortBy, $sortDirection, $request->filled('all'), $request->propertyType);
        return response($finish)->setStatusCode(200);
    }

        public function showType(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $type = $this->taxonomyService->getType($sortBy, $sortDirection, $request->filled('all'), $request->propertyType);
        return response($type)->setStatusCode(200);
    }

    /**
     * Display a listing of sizes with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showSize(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $size = $this->taxonomyService->getSize($sortBy, $sortDirection, $request->filled('all'), $request->propertyType);
        return response($size)->setStatusCode(200);
    }

    /**
     * Display a listing of finish with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showShade(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $shade = $this->taxonomyService->getShade($sortBy, $sortDirection, $request->filled('all'), $request->propertyType);
        return response($shade)->setStatusCode(200);
    }

    /**
     * Display a listing of feature with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showFeature(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $feature = $this->taxonomyService->getFeature($sortBy, $sortDirection, $request->filled('all'), $request->propertyType);
        return response($feature)->setStatusCode(200);
    }

    /**
     * Display a listing of color with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showColor(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $color = $this->taxonomyService->getColor($sortBy, $sortDirection, $request->filled('all'), $request->propertyType);
        return response($color)->setStatusCode(200);
    }

        /**
     * Display a listing of application with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showApplication(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $application = $this->taxonomyService->getApplication($sortBy, $sortDirection, $request->filled('all'), $request->propertyType);
        return response($application)->setStatusCode(200);
    }

    /**
     * Display a listing of office locations with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showOfficeLocations(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $officeLocations = $this->taxonomyService->getOfficeLocations($sortBy, $sortDirection);
        return response($officeLocations)->setStatusCode(200);
    }

    /**
     * Display a listing of property locations with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showPropertyLocations(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $propertyLocations = $this->taxonomyService->getPropertyLocations($sortBy, $sortDirection, $request->filled('all'));
        return response($propertyLocations)->setStatusCode(200);
    }

    /**
     * Display a listing of inquiry types with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showInquiryTypes(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $inquiryTypes = $this->taxonomyService->getInquiryTypes($sortBy, $sortDirection);
        return response($inquiryTypes)->setStatusCode(200);
    }

     /**
     * Display a listing of inquiry types with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showFormPages(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $formPages = $this->taxonomyService->getFormPages($sortBy, $sortDirection);
        return response($formPages)->setStatusCode(200);
    }


    /**
     * Display a listing of article categories with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showArticleCategories(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $articleCategories = $this->taxonomyService->getArticleCategories($sortBy, $sortDirection, $request->filled('all'));
        return response($articleCategories)->setStatusCode(200);
    }

    /**
     * Display a listing of employment types with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showEmploymentTypes(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $employmentTypes = $this->taxonomyService->getEmploymentTypes($sortBy, $sortDirection, $request->filled('all'));
        return response($employmentTypes)->setStatusCode(200);
    }

    /**
     * Display a listing of job locations with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showJobLocations(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $jobLocations = $this->taxonomyService->getJobLocations($sortBy, $sortDirection, $request->filled('all'));
        return response($jobLocations)->setStatusCode(200);
    }

    /**
     * Display a listing of after sales officers with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showAfterSalesOfficers(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $afterSalesOfficers = $this->taxonomyService->getAfterSalesOfficers($sortBy, $sortDirection);
        return response($afterSalesOfficers)->setStatusCode(200);
    }

    /**
     * Display a listing of after sales officers sorted alphabetically
     *
     * @return Response
     */
    public function showContactUsData(): Response
    {
        $afterSalesOfficers = $this->taxonomyService->getContactUsData();
        return response($afterSalesOfficers)->setStatusCode(200);
    }

    /**
     * Display a listing of after sales officers sorted alphabetically
     *
     * @return Response
     */
    public function showReferralTypeWeb(): Response
    {
        $referralTypes = $this->taxonomyService->getReferralTypesWeb();
        return response($referralTypes)->setStatusCode(200);
    }

     /**
     * Display a listing of after sales officers sorted alphabetically
     *
     * @return Response
     */
    public function showPriorityLocationWeb(): Response
    {
        $referralTypes = $this->taxonomyService->getPriorityLocationsWeb();
        return response($referralTypes)->setStatusCode(200);
    }

     /**
     * Display a listing of after sales officers sorted alphabetically
     *
     * @return Response
     */
    public function showInquiryTypeWeb(): Response
    {
        $inquiryTypes = $this->taxonomyService->getInquiryTypesWeb();
        return response($inquiryTypes)->setStatusCode(200);
    }


    /**
     * Display a listing of referral types with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showReferralTypes(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $referralTypes = $this->taxonomyService->getReferralTypes($sortBy, $sortDirection);
        return response($referralTypes)->setStatusCode(200);
    }

     /**
     * Display a listing of referral types with sorting options
     *
     * @param Request $request
     * @return Response
     */
    public function showPriorityLocations(Request $request): Response
    {
        $sortBy = $request->query('sortBy', 'created_at');
        $sortDirection = $request->query('sortDirection', 'desc');

        $priorityLocations = $this->taxonomyService->getPriorityLocations($sortBy, $sortDirection);
        return response($priorityLocations)->setStatusCode(200);
    }

    /**
     * Store a newly created taxonomy in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        // Base validation rules that apply to all taxonomy types
        $baseRules = [
            'type' => ['required', 'string', Rule::in([
                Taxonomy::TYPE_BRAND,
                Taxonomy::TYPE_MATERIAL,
                Taxonomy::TYPE_TYPE,
                Taxonomy::TYPE_PURPOSE,
                Taxonomy::TYPE_SIZE,
                Taxonomy::TYPE_FINISH,
                Taxonomy::TYPE_SHADE,
                Taxonomy::TYPE_FEATURE,
                Taxonomy::TYPE_COLOR,
                Taxonomy::TYPE_APPLICATION,
                Taxonomy::TYPE_PROPERTY_LOCATION,
                Taxonomy::TYPE_PROPERTY_STATUS,
                Taxonomy::TYPE_ARTICLE_CATEGORY,
                Taxonomy::TYPE_JOB_LOCATION,
                Taxonomy::TYPE_EMPLOYMENT,
                Taxonomy::TYPE_INQUIRY,
                Taxonomy::TYPE_AFTER_SALES,
                Taxonomy::TYPE_OFFICE,
                Taxonomy::TYPE_REFERRAL,
                Taxonomy::TYPE_PRIORITY_LOCATION,
                Taxonomy::TYPE_FORM_PAGE,
            ])],
        ];

        // Add name validation only if not after sales type
        if ($request->type !== Taxonomy::TYPE_AFTER_SALES) {
            $baseRules['name'] = ['required','string','max:50', Rule::unique('taxonomies')->whereNull('deleted_at')->where('type', $request->type)];
        }

        // Type-specific validation rules
        $typeSpecificRules = $this->getTypeSpecificRules($request->type);

        // Merge base rules with type-specific rules
        $rules = array_merge($baseRules, $typeSpecificRules);

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Filter validated data based on fillable fields
        $validatedData = $this->filterValidatedData($validator->validated(), $request->type);

        try {
            DB::beginTransaction();

            $taxonomy = Taxonomy::create($validatedData);
            if ($request->has('main_image')) {
                $this->addImages('taxonomy', $request, $taxonomy, 'main_image');
            }

            DB::commit();

            return response([
                'message' => 'Taxonomy created successfully',
                'data' => $taxonomy
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response([
                'message' => 'Error creating taxonomy',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get type-specific validation rules based on taxonomy type
     *
     * @param string|null $type
     * @return array
     */
    private function getTypeSpecificRules(?string $type): array
    {
        switch ($type) {
            case Taxonomy::TYPE_BRAND:
                return [];
            
            case Taxonomy::TYPE_MATERIAL:
                return [];
            
            case Taxonomy::TYPE_TYPE:
                return [];
            
            case Taxonomy::TYPE_PURPOSE:
                return [];
            
            case Taxonomy::TYPE_SIZE:
                return [];
            
            case Taxonomy::TYPE_FINISH:
                return [];
            
            case Taxonomy::TYPE_SHADE:
                return [];
            
            case Taxonomy::TYPE_FEATURE:
                return [];

            case Taxonomy::TYPE_COLOR:
                return [];
            
            case Taxonomy::TYPE_APPLICATION:
                return [];
            
            case Taxonomy::TYPE_INQUIRY:
                return [
                    'email_recipients' => 'required|array',
                    'email_recipients.*' => 'email'
                ];
            
            case Taxonomy::TYPE_FORM_PAGE:
                return [
                    'email_recipients' => 'required|array',
                    'email_recipients.*' => 'email'
                ];

            case Taxonomy::TYPE_OFFICE:
                return [
                    'location_type' => 'required|string|max:255',
                    'address' => 'required|string',
                    'contact_number' => 'required|string|max:255',
                    'map_url' => 'nullable|string'
                ];

            case Taxonomy::TYPE_AFTER_SALES:
                return [
                    'first_name' => 'required|string|max:125',
                    'last_name' => 'required|string|max:125',
                    'officer_type' => 'required|string|max:255',
                    'contact_number' => 'required|string|max:255',
                    'email' => 'required|email'
                ];

            default:
                return [];
        }
    }

    /**
     * Filter validated data based on taxonomy type
     *
     * @param array $validatedData
     * @param string $type
     * @return array
     */
    private function filterValidatedData(array $validatedData, string $type): array
    {
        $allowedFields = ['name', 'type'];

        switch ($type) {
            // case Taxonomy::TYPE_BRAND:
            //     $allowedFields = array_merge($allowedFields, ['property_type']);
            //     break;

            case Taxonomy::TYPE_INQUIRY:
                $allowedFields[] = 'email_recipients';
                break;

            case Taxonomy::TYPE_FORM_PAGE:
                    $allowedFields[] = 'email_recipients';
                    break;

            case Taxonomy::TYPE_OFFICE:
                $allowedFields = array_merge($allowedFields, [
                    'location_type',
                    'address',
                    'contact_number',
                    'map_url'
                ]);
                break;

            case Taxonomy::TYPE_AFTER_SALES:
                // Add combined name but keep original fields
                if (isset($validatedData['first_name'], $validatedData['last_name'])) {
                    $validatedData['name'] = trim($validatedData['first_name'] . ' ' . $validatedData['last_name']);
                }

                $allowedFields = array_merge($allowedFields, [
                    'first_name',
                    'last_name',
                    'officer_type',
                    'contact_number',
                    'email'
                ]);
                break;
        }

        return array_intersect_key($validatedData, array_flip($allowedFields));
    }

    /**
     * Display the specified taxonomy.
     *
     * @param Taxonomy $taxonomy
     * @return Response
     */
    public function show(Taxonomy $taxonomy): Response
    {
        return response($taxonomy->load('images'))->setStatusCode(200);
    }


    /**
     * Remove the specified taxonomy from storage.
     *
     * @param Taxonomy $taxonomy
     * @return Response
     */
    public function destroy(Taxonomy $taxonomy): Response
    {
        try {
            // Check if the taxonomy is being used anywhere before deletion
            // This would need to be implemented based on your specific relationships
            // if ($this->taxonomyService->isInUse($taxonomy)) {
            //     return response([
            //         'message' => 'Cannot delete taxonomy as it is currently in use',
            //     ], 422);
            // }

            $taxonomy->delete();

            return response([
                'message' => 'Taxonomy deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response([
                'message' => 'Error deleting taxonomy',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified taxonomy in storage.
     *
     * @param Request $request
     * @param Taxonomy $taxonomy
     * @return Response
     */
    public function update(Request $request, Taxonomy $taxonomy): Response
    {
        // Base validation rules that apply to all taxonomy types
        $baseRules = [
            'type' => ['sometimes', 'string', Rule::in([
                Taxonomy::TYPE_BRAND,
                Taxonomy::TYPE_MATERIAL,
                Taxonomy::TYPE_TYPE,
                Taxonomy::TYPE_PURPOSE,
                Taxonomy::TYPE_SIZE,
                Taxonomy::TYPE_FINISH,
                Taxonomy::TYPE_SHADE,
                Taxonomy::TYPE_FEATURE,
                Taxonomy::TYPE_COLOR,
                Taxonomy::TYPE_APPLICATION,
                Taxonomy::TYPE_PROPERTY_LOCATION,
                Taxonomy::TYPE_ARTICLE_CATEGORY,
                Taxonomy::TYPE_JOB_LOCATION,
                Taxonomy::TYPE_EMPLOYMENT,
                Taxonomy::TYPE_INQUIRY,
                Taxonomy::TYPE_AFTER_SALES,
                Taxonomy::TYPE_OFFICE,
                Taxonomy::TYPE_REFERRAL,
                Taxonomy::TYPE_PROPERTY_STATUS,
                Taxonomy::TYPE_PRIORITY_LOCATION,
                Taxonomy::TYPE_FORM_PAGE,
            ])],
        ];

        // Add name validation only if not after sales type
        if ($request->type !== Taxonomy::TYPE_AFTER_SALES) {
            $baseRules['name'] =  ['sometimes','string','max:50', Rule::unique('taxonomies')->whereNull('deleted_at')->where('type', $request->type)->ignore($taxonomy->id)];
        }

        // Get type-specific validation rules
        $typeSpecificRules = $this->getTypeSpecificUpdateRules($request->type ?? $taxonomy->type);

        // Merge base rules with type-specific rules
        $rules = array_merge($baseRules, $typeSpecificRules);

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Filter validated data based on fillable fields
        $validatedData = $this->filterValidatedData(
            $validator->validated(),
            $request->type ?? $taxonomy->type
        );

        try {
            $taxonomy->update($validatedData);

            if ($request->has('main_image')) {
                $this->updateImages('taxonomy', $request, $taxonomy, 'main_image');
            }

            return response([
                'message' => 'Taxonomy updated successfully',
                'data' => $taxonomy
            ], 200);
        } catch (\Exception $e) {
            return response([
                'message' => 'Error updating taxonomy',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get type-specific validation rules for update based on taxonomy type
     *
     * @param string|null $type
     * @return array
     */
    private function getTypeSpecificUpdateRules(?string $type): array
    {
        switch ($type) {
            case Taxonomy::TYPE_INQUIRY:
                return [
                    'email_recipients' => 'sometimes|array',
                    'email_recipients.*' => 'email'
                ];
            
            case Taxonomy::TYPE_FORM_PAGE:
                return [
                    'email_recipients' => 'sometimes|array',
                    'email_recipients.*' => 'email'
                ];

            case Taxonomy::TYPE_OFFICE:
                return [
                    'location_type' => 'sometimes|string|max:255',
                    'address' => 'sometimes|string',
                    'contact_number' => 'sometimes|string|max:255',
                    'map_url' => 'nullable|string'
                ];

            case Taxonomy::TYPE_AFTER_SALES:
                return [
                    'first_name' => 'sometimes|string|max:125',
                    'last_name' => 'sometimes|string|max:125',
                    'officer_type' => 'sometimes|string|max:255',
                    'contact_number' => 'sometimes|string|max:255',
                    'email' => 'sometimes|email'
                ];

            default:
                return [];
        }
    }
}
