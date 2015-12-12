<?php namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {

	// 複数代入のブラックリスト
	protected $guarded = ['id', 'seq', 'status', 'created_at', 'updated_at'];

	/**
	 * 1対多のリレーション
	 */
	public function project()
	{
		return $this->belongsTo('App\model\Project');
	}

}
