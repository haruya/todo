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
	 * プロジェクトステータス変更(Ajax)
	 */
	public function postStatus(Request $request)
	{
		$this->isAjax($request);
		$result = DB::transaction(function() use($request) {
			// ステータス変更
			$updateSql = "
				UPDATE
					projects
				SET
					status = (
						CASE
							WHEN status = 'done' THEN 'notyet'
							ELSE 'done'
						END
					)
				WHERE
					id = ?
			";
			DB::update($updateSql, [$request->id]);
			return $request->id;
		});
		return \Response:: json($result);
	}

	/**
	 * プロジェクト並び順変更(Ajax)
	 */
	public function postSort(Request $request)
	{
		$this->isAjax($request);
		$result = DB::transaction(function() use($request) {
			parse_str($request->project);
			// 並び順変更
			foreach ($project as $key => $val) {
				$project = Project::findOrFail($val);
				$project->seq = $key;
				$project->save();
			}
			return true;
		});
		return \Response:: json($result);
	}


	/**
	 * プロジェクト新規追加(Ajax)
	 */
	public function postCreate(Request $request)
	{
		$this->isAjax($request);
		$result = DB::transaction(function() use($request) {
			// seqの番号を取得
			$selectSql = "
				SELECT
					MAX(seq) + 1 as maxSeq
				FROM
					projects
			";
			$data = DB::select($selectSql);
			if ($data[0]->maxSeq != null) {
				$seq = $data[0]->maxSeq;
			} else {
				$seq = 0;
			}
			// プロジェクト新規追加
			$project = Project::create($request->all());
			$project->seq = $seq;
			$project->save();
			return $project->id;
		});
		return \Response::json($result);
	}

	/**
	 * プロジェクト編集(Ajax)
	 */
	public function postUpdate(Request $request)
	{
		$this->isAjax($request);
		$result = DB::transaction(function() use($request) {
			// プロジェクト編集
			$project = Project::findOrFail($request->id);
			$project->name = $request->name;
			$project->save();
			return $project->id;
		});
		return \Response::json($result);
	}

	/**
	 * プロジェクト削除(Ajax)
	 */
	public function postDelete(Request $request)
	{
		$this->isAjax($request);
		$result = DB::transaction(function() use($request) {
			// プロジェクト削除
			$project = Project::findOrFail($request->id);
			$project->delete();
			return $project->id;
		});
		return \Response::json($result);
	}

	/**
	 * Ajaxかどうかの判定
	 */
	private function isAjax($request)
	{
		if (!$request->ajax()) {
			abort(405);
		}
	}
}
