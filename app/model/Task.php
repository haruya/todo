<?php namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {

	// 複数代入のブラックリスト
	protected $fillable = ['project_id', 'seq'];

	/**
	 * 1対多のリレーション
	 */
	public function project()
	{
		return $this->belongsTo('App\model\Project');
	}

}
