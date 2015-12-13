@extends('app')

@section('content')
<div class="container">
  <h2 class="text-primary">プロジェクト一覧</h2>
  <p><span id="addProject" class="btn btn-primary">新規追加</span></p>
  <table id="projects" class="table table-bordered">
    <thead>
      <th>完了</th>
      <th>プロジェクト名</th>
      <th>操作</th>
    </thead>
    <tbody>
      @foreach ($projects as $project)
        <tr id="project_{{ $project->id }}" dara-id="{{ $project->id }}">
          <td><input type="checkbox" class="checkProject" /></td>
          <td>{{ $project->name }}</td>
          <td>
            {!! link_to('tasks/index/' . $project->id, '[タスク]', ['class' => 'tasksLink']) !!}
            <span class="editProject">[編集]</span>
            <span class="deleteProject">[削除]</span>
            <span class="projectDrag">[drag]</span>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<!-- ui-dialog -->
<div id="addProjectDialog" title="プロジェクト新規追加">
  <div class="form-group">
    <label for="name" class="control-label">プロジェクト名</label>
    <input type="text" id="name" class="form-control" value="" />
  </div>
  <div class="form-group">
    <input type="hidden" id="baseUrl" value="{{ url('/') }}" />
    <input type="button" id="addProjectSubmit" class="btn btn-primary" value="新規追加" />
  </div>
</div>
@stop