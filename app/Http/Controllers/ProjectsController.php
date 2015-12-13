<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\model\Project;
use DB;

class ProjectsController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * プロジェクト一覧
	 */
	public function getIndex()
	{
		$projects = Project::orderBy('seq', 'asc')->get();
		return view('projects.index')->with('projects', $projects);
	}

	/**
	 * プロジェクト新規追加(Ajax)
	 */
	public function postCreate(Request $request)
	{
		$result = DB::transaction(function() use($request) {
			// seqの番号を取得
			$data = DB::select("SELECT MAX(seq) + 1 as maxSeq FROM projects");
			if ($data[0]->maxSeq != null) {
				$seq = $data[0]->maxSeq;
			} else {
				$seq = 0;
			}

			// プロジェクト新規追加
			// DB::insert('insert into projects (name, seq, created_at, updated_at) values (?, ?, now(), now())', [$name, $seq]);
			$project = Project::create($request->all());
			$project->seq = $seq;
			$project->save();
			return $project->id;
		});
		return \Response::json($result);
	}

}
