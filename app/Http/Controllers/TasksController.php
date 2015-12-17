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
}
