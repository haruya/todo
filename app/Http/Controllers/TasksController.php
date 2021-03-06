<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\model\Task;
use App\model\Project;
use DB;

class TasksController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * タスク一覧
	 */
	public function getIndex($projectId)
	{
		// プロジェクトの存在チェック
		$project = $this->checkProject($projectId);
		if (!$project) {
			\Session::flash('flash_message', 'タスクが存在しません。');
			return redirect('/');
		}
		// タスク一覧取得
		$tasks = Task::where('project_id', $projectId)->orderBy('seq', 'asc')->get();
		// ステータス定義
		$statusList = array(
			'before_work' => '作業前',
			'working' => '作業中',
			'after_work' => '作業後'
		);
		// 優先度定義
		$priorityList = array(
			'10' => '低',
			'20' => '中',
			'30' => '高'
		);
		return view('tasks.index', compact('tasks', 'project', 'statusList', 'priorityList'));
	}


	/**
	 * タスク並び順変更(Ajax)
	 */
	public function postSort(Request $request)
	{
		$this->isAjax($request);
		$result = DB::transaction(function() use($request) {
			parse_str($request->task);
			// 並び順変更
			foreach ($task as $key => $val) {
				$task = Task::findOrFail($val);
				$task->seq = $key;
				$task->save();
			}
			return true;
		});
		return \Response:: json($result);
	}

	/**
	 * タスク新規追加(Ajax)
	 */
	public function postCreate(Request $request)
	{
		$this->isAjax($request);
		$result = DB::transaction(function() use ($request) {
			// seqの番号取得
			$selectSql = "
				SELECT
					MAX(seq) + 1 as maxSeq
				FROM
					tasks
				WHERE
					project_id = ?
			";
			$data = DB::select($selectSql, [$request->project_id]);
			if ($data[0]->maxSeq != null) {
				$seq = $data[0]->maxSeq;
			} else {
				$seq = 0;
			}
			// タスク新規追加
			$task = Task::create($request->all());
			$task->seq = $seq;
			$task->save();
			return $task;
		});
		return \Response::json($result);
	}

	/**
	 * タスク保存(変更)処理(Ajax)
	 */
	public function postUpdate(Request $request)
	{
		$this->isAjax($request);

		if (mb_strlen($request->worker, "UTF-8") > 32) {
			$result = "作業者は32文字以内で入力してください。";
			return \Response:: json($result);
		}

		if (mb_strlen($request->title, "UTF-8") > 64) {
			$result = "タイトルは64文字以内で入力してください。";
			return \Response:: json($result);
		}

		$result = DB::transaction(function() use($request) {
			// タスク保存(変更)
			$task = Task::findOrFail($request->id);
			$task->title = $request->title;
			$task->content = $request->content;
			$task->remarks = $request->remarks;
			$task->status = $request->status;
			$task->priority = $request->priority;
			$task->worker = $request->worker;
			$task->save();
			return true;
		});
		return \Response:: json($result);
	}


	/**
	 * タスク削除(Ajax)
	 */
	public function postDelete(Request $request)
	{
		$this->isAjax($request);
		$result = DB::transaction(function() use($request) {
			// タスク削除
			$task = Task::findOrFail($request->id);
			$task->delete();
			return $task->id;
		});
		return \Response::json($result);
	}

	/**
	 * プロジェクトの存在チェック
	 */
	private function checkProject($projectId)
	{
		$project = Project::find($projectId);
		if (count($project) > 0) {
			return $project;
		} else {
			return false;
		}
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
