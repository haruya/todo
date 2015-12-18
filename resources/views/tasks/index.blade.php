@extends('app')

@section('content')
<div class="container">
  <ul class="breadcrumb">
    <li>{!! link_to('/', 'プロジェクト一覧') !!}</li>
    <li class="active">「{{ $project->name }}」のタスク一覧</li>
  </ul>
  <h2 class="text-primary">「{{ $project->name }}」のタスク一覧</h2>
  @if (Session::has('flash_message'))
    <!-- フラッシュメッセージの表示 -->
    <p id="flash_message" class="alert alert-success">{{ Session::get('flash_message') }}</p>
  @endif
  @if ($errors->all())
    @foreach ($errors->all() as $error)
      <p class="alert alert-danger">{{ $error }}</p>
    @endforeach
  @endif
  <input type="hidden" id="baseUrl" value="{{ url('/') }}" />
  <input type="hidden" id="projectId" value="{{ $project->id }}" />
  <p><span id="addTask" class="btn btn-primary">新規追加</span></p>
  <table id="tasks" class="table table-bordered">
    <thead>
      <tr>
        <th class="text-center">ステータス</th>
        <th class="text-center">登録日</th>
        <th class="text-center">作業者</th>
        <th class="text-center">優先度</th>
        <th class="text-center">タイトル</th>
        <th class="text-center">内容</th>
        <th class="text-center">備考</th>
        <th class="text-center">操作</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($tasks as $task)
        <tr id="task_{{ $task->id }}" class="{{ $task->status }}" data-id="{{ $task->id }}">
          <td class="text-center">{!! Form::select('status', $statusList, $task->status, ['class' => 'status']) !!}</td>
          <td class="text-center">{{ $task->created_at->format('Y-m-d') }}</td>
          <td><input type="text" class="worker" value="{{ $task->worker }}" /></td>
          <td class="text-center">{!! Form::select('priority', $priorityList, $task->priority, ['class' => 'priority']) !!}</td>
          <td><input type="text" class="title" value="{{ $task->title }}" /></td>
          <td><textarea class="content">{{ $task->content }}</textarea></td>
          <td><textarea class="remarks">{{ $task->remarks }}</textarea></td>
          <td class="text-center">
            <span class="deleteTask">[削除]</span>
            <span class="taskDrag">[drag]</span>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@stop