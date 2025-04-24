<?php

namespace App\Services\Transaction;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\{
    Hash,
    Validator,
};
use App\Models\{
    User,
    Variation,
    Accessories,
    Transaction,
    TransactionItem,
    UserDetail,
    UserAddress,
    Discount,
    Role,
};
use App\Jobs\SendOrderConfirmationMail;
use App\Traits\GlobalTrait;

class TransactionService
{
    /**
     * @var GlobalTrait
     */
    use GlobalTrait;

    /**
     * TransactionService index
     * @param  Request $request
     * @return Response
     */
    public function index ($request): Response
    {
        $records = Transaction::when(isset($request->keyword), function ($q) use ($request) {
            $q->where('order_no', 'LIKE', '%'.strtolower($request->keyword).'%')
            ->orWhereHas('userDetail', function ($q) use ($request) {
                $q->where('full_name', 'LIKE', '%'.strtolower($request->keyword).'%');
            });
        })
        ->with([
            'user',
            'userDetail'
        ])
        ->when(isset($request->status), function ($q) use ($request) {
            $q->where('status', $request->status);
        })
        ->when(isset($request->sort_by), function ($q) use ($request) {
            if ($request->order_type == 'desc') {
                $q->orderByDesc($request->sort_by);
            }
            else {
                $q->orderBy($request->sort_by);
            }
        })
        ->when($request->filled('all') , function ($query, $request) {
            return $query->get();
        }, function ($query) {
            return $query->paginate(20);
        });

        return response([
            'records' => $records
        ]);
    }

    /**
     * TransactionService show
     * @param  Transaction $transaction
     * @param  Request $request
     * @return Response
     */
    public function show ($transaction, $request): Response
    {
        $this->generateLog($request->user(), "viewed this transaction ({$transaction->id}).");

        if ($request->type == 'accessories') {
            $transaction->load([
                'items.accessory',
                'items.variation',
                'items.discount',
                'user',
                'userDetail',
                'userAddress'
            ]);
        }

        return response([
            'record' => $transaction
        ]);
    }

    /**
     * TransactionService update
     * @param  Transaction $transaction
     * @param  Request $request
     * @return Response
     */
    public function update ($transaction, $request): Response
    {
        $validator = Validator::make($request->all(), [
            'remarks'=> 'sometimes',
            'item_id'   => 'sometimes',
            'item_exemption_amount'   => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $transaction->load([
            'items',
        ]);
        
        $total = (object) [
            'exemption_amount' => 0,
            'discounted_amount' => $transaction->total_discounted_amount,
            'original_amount' => $transaction->total_original_amount,
            'amount' => 0,
        ];

        foreach ($transaction->items as $key => $value) {
            $items = (object) [
                'id' => $value->id,
                'exemption_amount' => $value->exemption_amount,
                'discounted_amount' => $value->discounted_amount,
                'original_amount' => $value->original_amount,
                'amount' => $value->amount,
            ];

            if (isset($request->item_id)) {
                foreach ($request->item_id as $index => $data) {
                    if ($data == $value->id) {
                        if (isset($request->item_exemption_amount[$index])) {
                            $items->exemption_amount = number_format($request->item_exemption_amount[$index], '2', '.', '');
                        }
                    }
                }
            }

            // minus the discount and exemption to the amount
            $items->amount = number_format(($items->original_amount - $items->discounted_amount - $items->exemption_amount), 2, '.', '');

            // add all exemption and amount
            $total->exemption_amount = number_format($total->exemption_amount + $items->exemption_amount, 2, '.', '');
            $total->amount = number_format($total->amount + $items->amount, 2, '.', '');

            $value->update([
                'exemption_amount' => $items->exemption_amount,
                'amount' => $items->amount
            ]);
        }

        $transaction->update([
            'remarks' => $request->remarks,
            'total_exemption_amount' => $total->exemption_amount,
            'total_amount' => $total->amount,
        ]);

        $this->generateLog($request->user(), "updated this transaction ({$transaction->id}).");

        if ($request->type == 'accessories') {
            $transaction->load([
                'items.accessory',
                'items.variation',
                'items.discount',
                'user',
                'userDetail',
                'userAddress'
            ]);
        }

        return response([
            'record' => $transaction,
        ]);
    }

    /**
     * TransactionService productCheckout
     * @param  Request $request
     * @return Response
     */
    public function productCheckout ($request): Response
    {
        $validator = Validator::make($request->all(), [
            'transaction_type'  => 'required',
            'first_name'        => 'required',
            'last_name'         => 'required',
            'contact_number'    => 'required',
            'telephone_number'  => 'sometimes',
            'email'             => 'required',
            'discounted'        => 'required',
            'house_no'          => 'required',
            'street'            => 'required',
            'zip_code'          => 'required',
            'region'            => 'required',
            'city'              => 'required',
            'barangay'          => 'required',
            'accessories_id'    => 'required',
            'accessories_variation_id'    => 'required',
            'accessories_quantity'    => 'required',
            'accessories_discount_id'    => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $role = Role::where('identifier', 'customer')->first();

        if (!$role) {
            $role = Role::create([
                'name' => 'Customer',
                'identifier' => 'customer',
                'type' => 'customer',
                'permissions' => null,
            ]);
        }

        // check if user exists
        $user = User::where('email', $request->email)
        ->first();
        if (!$user) {
            $member_id = str_pad( date('Y-m', strtotime('now')) . '-' .strtoupper(str_random(6)), 5 , 0, STR_PAD_LEFT);
            $full_name = sprintf('%s %s',
                $request->first_name,
                $request->last_name
            );

            $user = User::create([
                'email' => $request->email,
                'password' => 'default-password',
                'enabled' => 0,
                'role_id' => $role->id,
            ]);
            
            $user->userDetail()->create([
                'member_id' => $member_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'full_name' => $full_name,
                'contact_number' => $request->contact_number,
                'telephone_number' => $request->telephone_number,
                'slug' => $this->slugify($full_name, 'UserDetail'),
                'discounted' => $request->discounted
            ]);
        }
        else {
            $full_name = sprintf('%s %s',
                $request->first_name,
                $request->last_name
            );

            $user->userDetail()->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'full_name' => $full_name,
                'contact_number' => $request->contact_number,
                'telephone_number' => $request->telephone_number,
                'slug' => $this->slugify($full_name, 'UserDetail', $user->userDetail->id),
                'discounted' => $request->discounted
            ]);

        }
        
        // user address
        $userAddress = $user->addresses()->create([
            'house_no' => $request->house_no,
            'street' => $request->street,
            'zip_code' => $request->zip_code,
            'region' => $request->region,
            'city' => $request->city,
            'barangay' => $request->barangay,
        ]);

        $transaction = null;

        switch ($request->transaction_type) {
            case 'accessories':
                $transaction = $this->accessoriesCheckout($request, $user, $userAddress);
                break;
        }

        return response([
            'record' => $transaction
        ]);
    }

    /**
     * TransactionService accessoriesCheckout
     * @param  Request $request
     * @param  User $user
     * @param  UserAddress $userAddress
     */
    public function accessoriesCheckout($request, $user, $userAddress)
    {
        $transaction = null;
        $transaction_items = [];
        $total = (object) [
            'quantity' => 0,
            'exemption_amount' => 0,
            'discounted_amount' => 0,
            'original_amount' => 0,
            'amount' => 0,
        ];

        if (isset($request->accessories_id)) {
            foreach ($request->accessories_id as $key => $value) {  
                // check if valid accessories
                $accessoriesData = Accessories::select('id','title','price','enabled')
                ->where([
                    'id' => $value,
                    'enabled' => 1
                ])
                ->first();
                if ($accessoriesData) {
                    $items = (object) [
                        'accessoriedData' => $accessoriesData,
                        'variationData' => null,
                        'discountData' => null,
                        'quantity' => isset($request->accessories_quantity[$key]) ? $request->accessories_quantity[$key] : 0,
                        'exemption_amount' => 0,
                        'discounted_amount' => 0,
                        'original_amount' => 0,
                        'amount' => 0,
                    ];

                    // check if has variation
                    if (isset($request->accessories_variation_id[$key])) {
                        $items->variationData = Variation::where([
                            'id' => $request->accessories_variation_id[$key],
                            'product_id' => $accessoriesData->id,
                            'enabled' => 1
                        ])
                        ->first();
                    }

                    // computation of accessories/varitions amount * quantities
                    $items->original_amount = number_format((float) ($items->variationData ? $items->variationData->price : $accessoriesData->price ) * (int) $items->quantity, 2, '.', '');

                    // check if has discount
                    if (isset($request->accessories_discount_id[$key])) {
                        $items->discountData = Discount::where([
                            'id' => $request->accessories_discount_id[$key],
                            'expired' => 0,
                            'enabled' => 1
                        ])
                        ->whereDate('start_date', '<=', date('Y-m-d'))
                        ->whereDate('end_date', '>=', date('Y-m-d'))
                        ->first();

                        // computation of total discount amount
                        if ($items->discountData) {
                            switch ($items->discountData->type) {
                                case 'flat-rate':
                                    $items->discounted_amount = (float) $items->discountData->amount;
                                    break;
                                case 'percent':
                                    $percent = (float) $items->discountData->amount / 100;
                                    $items->discounted_amount = number_format((float) $items->original_amount * (float) $percent, 2, '.', '');
                                    # code...
                                    break;
                            }
                        }

                        $items->amount = number_format($items->original_amount - $items->discounted_amount, 2, '.', '');
                    } else {
                        $items->amount = number_format($items->original_amount, 2, '.', '');
                    }

                    // add all variables
                    $total->quantity = ($total->quantity + $items->quantity);
                    $total->exemption_amount = number_format($total->exemption_amount + $items->exemption_amount, 2, '.', '');
                    $total->discounted_amount = number_format($total->discounted_amount + $items->discounted_amount, 2, '.', '');
                    $total->original_amount = number_format($total->original_amount + $items->original_amount, 2, '.', '');
                    $total->amount = number_format($total->amount + $items->amount, 2, '.', '');

                    array_push($transaction_items, $items);
                }
            }
        }

        $order_no = str_pad( date('Y-m', strtotime('now')) . '-' .strtoupper(str_random(6)), 5 , 0, STR_PAD_LEFT);

        // create transactions
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'user_address_id' => $userAddress->id,
            'status' => 'completed',
            'order_no' => $order_no,
            'total_quantity' => $total->quantity,
            'total_exemption_amount' => $total->exemption_amount,
            'total_discounted_amount' => $total->discounted_amount,
            'total_original_amount' => $total->original_amount,
            'total_amount' => $total->amount,
            'remarks' => null,
        ]);

        foreach ($transaction_items as $key => $value) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $value->accessoriedData->id,
                'product_variation_id' => $value->variationData ? $value->variationData->id : null, 
                'discount_id' => $value->discountData ? $value->discountData->id : null, 
                'quantity' => $value->quantity,
                'exemption_amount' => $value->exemption_amount,
                'discounted_amount' => $value->discounted_amount,
                'original_amount' => $value->original_amount,
                'amount' => $value->amount,
            ]);
        }

        $transaction->load(['user', 'userDetail','items.accessory', 'items.variation', 'items.discount']);

        SendOrderConfirmationMail::dispatch($transaction);

        return $transaction;
    }
}
