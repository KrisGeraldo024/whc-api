<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AUBPaymentController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PaymentPlatformController;
use App\Http\Controllers\InquiryController;

Route::group(['prefix' => 'v1', 'middleware' => 'throttle:1000,1'], function () {
    Route::get('test', 'TestController@test');
    // Global
    Route::group(['prefix' => 'global'], function () {
        Route::group(['prefix' => 'user', 'controller' => 'UserController'], function () {
            Route::group(['middleware' => 'auth.global'], function () {
                Route::post('logout', 'logout');
                Route::get('check-token', 'checkToken');
            });
            Route::post('login', 'login');
        });
    });

    // CMS
    Route::group(['prefix' => 'cms', 'middleware' => 'auth.admin'], function () {

        #import template
        Route::group(['prefix' => 'import-template'], function () {
            Route::post('import-article', 'ArticleController@importArticles');
        });
        # Batch Upload
        Route::apiResource('batch-upload', 'BatchUploadController');


        Route::apiResource('dashboard', 'DashboardController');

        //page categories
        Route::get('page-categories', 'PageController@getCategories');

        //page sections
        Route::apiResource('page-section', 'PageSectionController');

        //biller
        Route::apiResource('billers', 'BillerController');

        //payments
        Route::apiResource('payment-platforms', 'PaymentPlatformController');
        Route::apiResource('payment-channels', 'PaymentChannelController');
        Route::apiResource('payment-methods', 'PaymentMethodController');
        Route::apiResource('payment-options', 'PaymentOptionController');
        Route::apiResource('payment-types', 'PaymentTypeController');

        //emails
        Route::apiResource('emails', 'EmailController');
        //offices
        Route::apiResource('offices', 'OfficeController');

        //testimonials
        Route::apiResource('testimonials', 'TestimonialController');

        //award
        Route::apiResource('awards', 'AwardController');

        //clients
        Route::apiResource('clients', 'ClientController');

        //cli stories
        Route::apiResource('stories', 'StoryController');

        //advantage
        Route::apiResource('advantages', 'AdvantageController');

        //award
        Route::apiResource('philosophies', 'PhilosophyController');

        //executive
        Route::apiResource('executives', 'ExecutiveController');

        //board
        Route::apiResource('boards', 'BoardController');

        //Property
        Route::apiResource('properties', 'PropertyController');

        //Units
        Route::apiResource('units', 'UnitController');

        //Landmarks
        Route::apiResource('landmarks', 'LandmarkController');

        //Projects
        Route::apiResource('projects', 'ProjectController');

        //Projects
        Route::apiResource('vicinities', 'VicinityController');

        //Amenities
        Route::apiResource('amenities', 'AmenityController');

        //floorplan
        Route::apiResource('floorplan', 'FloorplanController');

        //architects perspective
        Route::apiResource('architects', 'ArchitectController');

        //architects perspective
        Route::apiResource('mission-vision', 'MissionVisionController');

        //construction updates
        Route::apiResource('construction-updates', 'ConstructionUpdateController');

        //project awards
        Route::apiResource('project-awards', 'ProjectAwardController');
        //property sub category
        Route::apiResource('property-subcategories', 'PropertySubcategoryController');

        //career
        Route::apiResource('careers', 'CareerController');

        //business units
        Route::apiResource('business-units', 'BusinessUnitController');

        //business units directory
        Route::apiResource('business-units-directory', 'BusinessUnitsDirectoryController');



        //website settings
        Route::group(['prefix' => 'setting', 'controller' => 'WebsiteSettingController'], function () {
            Route::get('show', 'show');
            Route::post('manage', 'manage');
        });


        /*TAXONOMY*/
        // Taxonomy routes
        Route::group(['prefix' => 'taxonomies', 'controller' => 'TaxonomyController'], function () {
            Route::get('brand', 'showBrand')->name('brand');
            Route::get('material', 'showMaterial')->name('material');
            Route::get('type', 'showType')->name('type');
            Route::get('purpose', 'showPurpose')->name('purpose');
            Route::get('size', 'showSize')->name('size');
            Route::get('finish', 'showFinish')->name('finish');
            Route::get('shade', 'showShade')->name('shade');
            Route::get('feature', 'showFeature')->name('feature');
            Route::get('color', 'showColor')->name('color');
            Route::get('application', 'showApplication')->name('application');
            Route::get('/office-locations', 'showOfficeLocations')->name('office.locations');
            Route::get('/property-statuses', 'showPropertyStatuses')->name('property.statuses');
            Route::get('/property-locations', 'showPropertyLocations')->name('property.locations');
            Route::get('/inquiry-types', 'showInquiryTypes')->name('inquiry.types');
            Route::get('/article-categories', 'showArticleCategories')->name('article.categories');
            Route::get('/employment-types', 'showEmploymentTypes')->name('employment.types');
            Route::get('/job-locations', 'showJobLocations')->name('job.locations');
            Route::get('/after-sales-officers', 'showAfterSalesOfficers')->name('after.sales.officers');
            Route::get('/referral-types', 'showReferralTypes')->name('referral.types');
            Route::get('/priority-locations', 'showPriorityLocations')->name('priority.types');
            Route::get('/form-pages', 'showFormPages')->name('form.pages');
            Route::get('/{taxonomy}', 'show')->name('taxonomy.show');
            Route::put('/{taxonomy}', 'update')->name('taxonomy.update');
            Route::delete('/{taxonomy}', 'destroy')->name('taxonomy.destroy');
            Route::post('/', 'store')->name('taxonomy.store');
        });


        //locations
        Route::apiResource('locations', 'LocationController');

        //property type
        Route::apiResource('project-statuses', 'ProjectStatusController');
        //departments
        Route::apiResource('departments', 'DepartmentController');

        // Page
        Route::group(['prefix' => 'page', 'controller' => 'PageController'], function () {
            Route::get('list', 'index');
            Route::post('add', 'store');
            Route::get('show/{page}', 'show');
            Route::patch('update/{page}', 'update');
            Route::delete('delete/{page}', 'destroy');
            // Banner
            Route::group(['prefix' => '{page}/banner', 'controller' => 'PageBannerController'], function () {
                Route::get('list', 'index');
                Route::post('add', 'store');
                Route::get('show/{banner}', 'show');
                Route::patch('update/{banner}', 'update');
                Route::delete('delete/{banner}', 'destroy');
            });
            // CTA
            Route::group(['prefix' => '{page}/cta', 'controller' => 'PageCtaController'], function () {
                Route::get('list', 'index');
                Route::post('add', 'store');
                Route::get('show/{cta}', 'show');
                Route::patch('update/{cta}', 'update');
                Route::delete('delete/{cta}', 'destroy');
            });

            // card
            Route::group(['prefix' => '{page}/card', 'controller' => 'PageCardController'], function () {
                Route::get('list', 'index');
                Route::post('add', 'store');
                Route::get('show/{card}', 'show');
                Route::patch('update/{card}', 'update');
                Route::delete('delete/{card}', 'destroy');
            });

            //faq
            Route::group(['prefix' => '{page}/faq', 'controller' => 'PageFaqController'], function () {
                Route::get('list', 'index');
                Route::post('add', 'store');
                Route::get('show/{faq}', 'show');
                Route::patch('update/{faq}', 'update');
                Route::delete('delete/{faq}', 'destroy');
            });

            //uvp
            Route::group(['prefix' => '{page}/uvp', 'controller' => 'PageUvpController'], function () {
                Route::get('list', 'index');
                Route::post('add', 'store');
                Route::get('show/{uvp}', 'show');
                Route::patch('update/{uvp}', 'update');
                Route::delete('delete/{uvp}', 'destroy');
            });

            //file
            Route::group(['prefix' => '{page}/file', 'controller' => 'PageFileController'], function () {
                Route::get('list', 'index');
                Route::post('add', 'store');
                Route::get('show/{file}', 'show');
                Route::patch('update/{file}', 'update');
                Route::delete('delete/{file}', 'destroy');
            });

            // file
            Route::group(['prefix' => '{page}/tag', 'controller' => 'PageTagController'], function () {
                Route::get('list', 'index');
                Route::post('add', 'store');
                Route::get('show/{tag}', 'show');
                Route::patch('update/{tag}', 'update');
                Route::delete('delete/{tag}', 'destroy');
            });
            Route::group(['prefix' => 'tag', 'controller' => 'PageTagController'], function () {
                Route::get('list', 'getTagList');
            });

            // Tab
            Route::group(['prefix' => '{page}/tab', 'controller' => 'PageTabController'], function () {
                Route::get('list', 'index');
                Route::post('add', 'store');
                Route::get('show/{tab}taxonomiestab}', 'destroy');
            });
        });

        // User
        Route::apiResource('users', 'UserController');

        // Role
        Route::apiResource('roles', 'RoleController');

        // Page
        Route::apiResource('pages', 'PageController');

        // History
        Route::apiResource('histories', 'HistoryController');

        // Stat
        Route::apiResource('stats', 'StatController');

        // Faq
        Route::apiResource('faqs', 'FaqController');

        // Service
        Route::apiResource('services/categories', 'ServiceCategoryController');
        Route::apiResource('services', 'ServiceController');
        Route::apiResource('services.items', 'ServiceItemController');

        // Video
        Route::apiResource('videos/categories', 'VideoCategoryController');
        Route::apiResource('videos', 'VideoController');

        // Branch
        Route::apiResource('branches/regions', 'BranchRegionController');
        Route::apiResource('branches/regions.vicinities', 'BranchVicinityController');
        Route::apiResource('branches', 'BranchController');

        // Promo
        Route::apiResource('promos', 'PromoController');

        // Article
        Route::apiResource('articles/categories', 'ArticleCategoryController');
        Route::apiResource('articles', 'ArticleController');

        // Accessories
        Route::apiResource('accessories', 'AccessoriesController');
        Route::apiResource('accessories.items', 'AccessoriesItemController');

        // Hearing Aid
        Route::apiResource('hearing-aids/categories', 'HearingAidCategoryController');
        Route::apiResource('hearing-aids', 'HearingAidController');
        Route::apiResource('hearing-aids.items', 'HearingAidItemController');

        // Variations
        Route::apiResource('variations', 'VariationController');
        Route::apiResource('variations.items', 'VariationItemController');

        // Discounts
        Route::apiResource('discounts', 'DiscountController');

        // transactions
        Route::apiResource('transactions', 'TransactionController');

        // Appointments
        Route::apiResource('appointments', 'AppointmentController');

        // Form Details
        Route::apiResource('formdetails', 'FormDetailsController');

        // Page Banner
        Route::apiResource('pagebanners', 'PageBannerController');

        // Import
        Route::group(['prefix' => 'import', 'controller' => 'ImportController'], function () {
            Route::post('upload', 'upload');
        });

        // Inquiry
        Route::apiResource('inquiries', 'InquiryController')->only('index', 'update');
        Route::get('/export-inquiries', [InquiryController::class, 'exportInquiries']);
        Route::apiResource('subscribes', 'SubscribeController')->only('index');

        // Extra
        Route::group(['prefix' => 'extra', 'controller' => 'ExtraController'], function () {
            Route::get('dashboard', 'dashboard');
            Route::delete('delete-image/{image}', 'deleteImage');
            Route::get('copy-image/{type}', 'copyAllImages');
            Route::post('delete-item', 'deleteItem');
        });

        Route::apiResource('uvp_details', 'UvpDetailsController');
        Route::apiResource('page_templates', 'PageTemplateController');
        Route::apiResource('getPageTemplate', 'PageTemplateController');

        /*TAXONOMY*/
        Route::apiResource('taxServices', 'TaxServicesController');
        Route::apiResource('taxServicesDetails', 'TaxServicesDetailsController');

        Route::apiResource('taxRemittances', 'TaxRemittancesController');
        Route::apiResource('taxRemittancesDetails', 'TaxRemittancesDetailsController');

        Route::apiResource('taxInformations', 'TaxInformationsController');
        Route::apiResource('taxInformationsDetails', 'TaxInformationsDetailsController');

        Route::apiResource('taxTravels', 'TaxTravelsController');
        Route::apiResource('taxCurrencies', 'TaxCurrenciesController');
        Route::apiResource('taxTelcos', 'TaxTelcosController');

        Route::apiResource('getRemittanceList', 'TaxRemittancesController');
        Route::apiResource('getInformationList', 'TaxInformationsController');
        Route::apiResource('getTravelList', 'TaxTravelsController');
        Route::apiResource('getCurrencyList', 'TaxCurrenciesController');
        Route::apiResource('getTelcoList', 'TaxTelcosController');

        // PaymentMethod   
        Route::apiResource('payment-methods', 'PaymentMethodController');
        // Regular CRUD routes for payment platforms
        Route::apiResource('payment-methods.payment-platforms', 'PaymentPlatformController');
        // Custom route for getting platforms by payment method
        Route::get('payment-methods/{payment_method}/payment-platforms', [PaymentPlatformController::class, 'byPaymentMethod'])
            ->name('payment-methods.platforms.index');

        // Reordering
        Route::post('re-order', 'ExtraController@reOrder');
    });

    // Web
    Route::group(['prefix' => 'web'], function () {

        // Captcha
        Route::post('captcha-verification', 'ExtraController@captchaVerification');

        // Inquiry
        Route::post('contact-us', 'InquiryController@inquiry');

        // // Subscribe
        // Route::group(['prefix' => 'subscribe', 'controller' => 'SubscribeController'], function () {
        //     Route::post('/on', 'subscribe');
        //     Route::post('/off', 'unsubscribe');
        // });

        // // Geo data
        // Route::get('geo-data', 'PageController@getGeoData');

        // Page
        Route::group(['prefix' => 'page', 'controller' => 'PageController'], function () {
            Route::get('global-data', 'globalData');
            Route::get('biller-search', 'billerSearch');
            Route::get('project-search', 'projectSearch');
            Route::post('page-data/{identifier}', 'pageData');
            Route::get('sitemap', 'sitemap');
        });

        //Awards
        Route::get('getAwards', 'AwardController@getAwards');

        // Contact Us
        Route::post('submit-inquiry',   'InquiryController@sendInquiry');
        Route::post('sales-inquiry',   'InquiryController@salesInquiry');
        Route::post('submit-application', 'InquiryController@sendApplication');
        Route::post('broker-application', 'InquiryController@brokerForm');

        //Article
        Route::get('article', 'ArticleController@getArticle');
        Route::get('articles', 'ArticleController@getAll');

        // File
        Route::post('download-floorplan', 'PdfController@generatePdf');
        Route::post('download-file', 'FileController@download');

        Route::post('download-construction', 'FileController@downloadConstructionUpdates');

        Route::get('/contact-us-data', 'TaxonomyController@showContactUsData');

        Route::get('properties', 'PropertyController@getAll');
        Route::get('property', 'PropertyController@getProperty');
        Route::get('unit', 'UnitController@getUnit');
        Route::get('/properties/{property}/related/{related}', 'PropertyController@getRelateds');
        Route::get('property-list', 'PropertyController@getPropertyList');

        Route::get('careers', 'CareerController@getCareers');
        Route::get('career', 'CareerController@getCareer');

        Route::get('payment-methods', 'PaymentMethodController@getMethods');
        Route::get('payment-methods/{payment_method}/payment-platforms', [PaymentPlatformController::class, 'byPaymentMethod'])
            ->name('payment-methods.platforms.index');
        Route::get('payment-platform', 'PaymentPlatformController@getPaymentPlatform');

        Route::get('/referral-types', 'TaxonomyController@showReferralTypeWeb');
        Route::get('/priority-locations', 'TaxonomyController@showPriorityLocationWeb');
        Route::get('/inquiry-types', 'TaxonomyController@showInquiryTypeWeb');
        Route::get('/property-locations', 'PropertyController@getLocationsByType');

        // Aub Payment 

        Route::prefix('aub-payment')->group(function () {
            Route::post('initiate', [AUBPaymentController::class, 'initiatePayment']);
            Route::post('callback', [AUBPaymentController::class, 'callback']);
            Route::post('notify', [AUBPaymentController::class, 'notify']);
        });

        Route::get('/article-categories', 'TaxonomyController@showArticleCategories')->name('article.categories');
        Route::get('/article-list', 'ArticleController@getArticleList');

        // Newsletter   
        Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);

        // Website Settings
        Route::get('website-settings', 'WebsiteSettingController@show');

        Route::post('search', 'PropertyController@search');
        Route::post('get-suggested', 'PropertyController@getSuggesteds');
    });
});
