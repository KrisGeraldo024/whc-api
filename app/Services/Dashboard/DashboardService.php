<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use App\{
  Traits\GlobalTrait,
  Models\Log,
};
use Carbon\Carbon;
use DB;

use App\Models\{
    Page,
    ServiceCategory,
    Accessories,
    Service,
    Video,
    Promo,
    HearingAid,
    HearingAidCategory,
    Province,
    Municipality,
    Barangay,
    Branch,
    BranchRegion,
    BranchVicinity,
    Faq,
    History,
    Article,
    ArticleCategory,
    VideoCategory,
    User,


    Email,
    Office,
    Stat,
    Award,
    Testimonial,
    Property,
    Story,
    Board,
    Executive,
    Advantage,
    Philosophy,
    Project,
    Vicinity,
    Architect,
    Amenity,
    Floorplan,
    MissionVision,
    ConstructionUpdate,
    Location,
    ProjectStatus,
    PropertySubcategory,
    ProjectAward,
    WebsiteSetting,
    
};

class DashboardService
{
  use GlobalTrait;

  private $queryRows = 10;

  /**
   * Retrieves logs and related records for display on the logs index page.
   * 
   * @param object $request The request object.
   * 
   * @return Response The response object containing logs and related records.
   */
  public function index(object $request): Response
  {
    // Set the default page to 1 if not specified in the request.
    $page = 1;
    if ($request->has('page')) {
      $page = $request->page;
    }

    $records = [];
    //at a glance
    // $records['pages'] = Page::count();
    // $records['users'] = User::count();
    // $records['videos'] = Video::count();
    // $records['articles'] = Article::count();

    // //get user list
    // $records['user_list'] = User::select('id', 'email', 'role_id')
    //     ->when($request->filled('role_type'), function ($query) use ($request) {
    //         $query->whereHas('role', function ($query) use ($request) {
    //             $query->whereIn('type', [$request->role_type]);
    //         });
    //     })
    //     ->with(['role' => function ($query) {
    //             $query->select('id', 'name');
    //         }])
    //     ->get();

    //     $records['properties'] = Property::select('title', 'slug')
    //     ->orderBy('order')
    //     ->get();

    //     // Get project count
    //     $records['projects_data'] = [];
    //     foreach ($records['properties'] as $property) {
    //         $projectsData = Property::where('slug', $property->slug)
    //             ->withCount('projects') 
    //             ->first();
            
    //         if ($projectsData) {
    //             $records['projects_data'][] = $projectsData;
    //         }
    //     }
        
    //     // Find the projects with titles "Condominiums", "Subdivisions", "Beachtown Residences"
    //     $condominiums = null;
    //     $subdivisions = null;
    //     $beachtownResidences = null;
        
    //     foreach ($records['projects_data'] as $key => $project) {
    //         switch ($project->slug) {
    //             case 'condominiums':
    //                 $condominiums = $project;
    //                 break;
    //             case 'subdivisions':
    //                 $subdivisions = $project;
    //                 break;
    //             case 'beachtown-residences':
    //                 $beachtownResidences = $project;
    //                 break;
    //         }
    //     }
        
    //     // Set the "residences" project count to the sum of the counts of the above projects
    //     $residencesCount = $condominiums->projects_count + $subdivisions->projects_count + $beachtownResidences->projects_count;
        
    //     // Set the "residences" project count in the original data
    //     foreach ($records['projects_data'] as $key => &$project) {
    //         if ($project->slug === 'residences') {
    //             $project->projects_count = $residencesCount;
    //         }
    //     }
        
    //     // Remove the projects with titles "Condominiums", "Subdivisions", "Beachtown Residences"
    //     $projectsToRemove = ['condominiums', 'subdivisions', 'beachtown-residences'];
        
    //     $records['projects_data'] = array_values(array_filter($records['projects_data'], function ($project) use ($projectsToRemove) {
    //         return !in_array($project->slug, $projectsToRemove);
    //     }));
        
    //     // Now $records['projects_data'] contains the modified data as an array of objects
        




    

    // //GET ALL AWARDS, COMBINE RESULTS AND MERGE 
    // $awards = Award::select('year', \DB::raw('count(*) as count'))->groupBy('year');
    // $projectAwards = ProjectAward::select('year', \DB::raw('count(*) as count'))->groupBy('year');
    // $combinedAwards = $awards->union($projectAwards)->get();
    // $uniqueAwards = collect($combinedAwards)->groupBy('year')->map(function ($items) {
    //     return [
    //         'year' => $items[0]['year'],
    //         'count' => $items->sum('count'),
    //     ];
    // })->sortBy('year')->values()->all();
    // $records['awards'] = $uniqueAwards;



    // //get project status
    // $records['project_status'] = ProjectStatus::select('id', 'name')->get();


    // $records['project_status_data'] = Project::select('project_statuses.id','project_statuses.name as status_name', DB::raw('count(*) as count'))
    // ->join('project_statuses', 'projects.project_status_id', '=', 'project_statuses.id')
    // ->groupBy('project_statuses.name','project_statuses.id')
    // ->get();



    // $records['project_location_data'] = Project::select('locations.name as location_name', DB::raw('count(*) as count'))
    // ->join('locations', 'projects.location_id', '=', 'locations.id')
    // ->groupBy('locations.name')
    // ->get();




    $records['logs'] = Log::orderBy('created_at', 'DESC')
    ->orderBy('updated_at', 'DESC')
    ->with(['user' => function ($q) {
      $q->with(['images', 'userDetail']);
    }])
    ->paginate($this->queryRows);
    
 

    $msg = "viewed page {$page} of dashboard";

    return response([
    //   'message' => $this->generateLog($request->user(), $msg),
      'records' => $records['logs']
    ]);
  }


}