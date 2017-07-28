<?php

namespace App\Http\Controllers;

use App\Experiments;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $user = Auth::User();
        $experiments = $user->experiments()->withTrashed()->orderby('created_at','desc')->get();
        return view('home')->with('experiments', $experiments);
    }

    /**
     * Create new experiment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('add');
    }

    /**
     * Edit experiment.
     *
     * @param  int  $id  ID of experiment to edit.
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $experiment = Experiments::find($id)->toArray();
        $experiment['rules'] = json_decode($experiment['rules']);
        $experiment['options'] = json_decode($experiment['options']);
        return view('edit')->with(array(
            'id' => $id,
            'experiment' => $experiment
        ));
    }

    /**
     * Re-active an experiment that was deactivated.
     *
     * @param   Request  $request  
     * @param   int   $id  ID of experiment.
     * @return  Response
     */
    public function activate(Request $request, $id) {
        $experiment = Experiments::find($id);
        $experiment->deleted_at = Carbon::now()->toDateTimeString();
        $experiment->save();
        return redirect('/rule');
    }

    /**
     * Deactivate an experiment by soft deleting.
     *
     * @param   Request  $request   
     * @param   int   $id  ID of experiment.
     * @return  Response
     */
    public function deactivate(Request $request, $id) {
        $experiment = Experiments::withTrashed()->find($id);
        $experiment->deleted_at = null;
        $experiment->save();
        return redirect('/rule');
    }

    /**
     * Update the experiment.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $exp_id = $id;
        $exp_name = $request['exp_name'];
        $custom_options  = json_decode($request['custom_options']);

        $experiment = Experiments::find($exp_id);
        $experiment->name = $exp_name;
        $experiment->rules = $custom_options->rules;
        $experiment->options = $custom_options->options;
        $experiment->save();
        return redirect('/rule');
    }

    /**
     * Store the experiment.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $exp_name = $request['exp_name'];
        $custom_options  = json_decode($request['custom_options']);
        $user = Auth::User();

        $experiment = new Experiments();
        $experiment->user_id = $user->id;
        $experiment->name = $exp_name;
        $experiment->rules = $custom_options->rules;
        $experiment->options = $custom_options->options;
        $experiment->save();
        return redirect('/rule');

    }

    /**
     * Upload file to directory path of env('UPLOAD_DIR')
     *
     * @param  Request  $request  
     * @return  Response
     */
    public function FileUpload(Request $request) {
        $id = $_POST['id'];
        $upload_dir = env('UPLOAD_DIR');
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $result = array();
        $path_parts = pathinfo($_FILES['files']['name']);
        $file_extension = isset($path_parts['extension']) ? '.'.$path_parts['extension']:'';
        $filename = $path_parts['filename'].'_'.time().$file_extension;
        $file_des = $upload_dir.'/'.$filename;

        if (move_uploaded_file($_FILES['files']['tmp_name'], $file_des)) {
            $result = array(
                'id'            => $id,
                'type'          => $_FILES['files']['type'],
                'filename'      => $filename,
                'org_filename'  => $_FILES['files']['name']
            );
        }
        return response()->success($result);
    }
}
