$(function() {

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // プロジェクト新規追加処理
  $('#addProjectSubmit').click(function() {
    $(this).attr('disabled', 'disabled');
    $('#addProjectDialog #nameErr').remove();
    var error = false;
    var name = $('#addProjectDialog #name').val();
    if (name.length == 0) {
      $('#addProjectDialog #name').parent().after('<p id="nameErr" class="alert alert-danger">プロジェクト名は入力必須です。</p>');
      error = true;
    } else if (name.length > 64) {
      $('#addProjectDialog #name').parent().after('<p id="nameErr" class="alert alert-danger">プロジェクト名は64文字以内で入力してください。</p>');
      error = true;
    }
    if (error == false) {
    	var baseUrl = $('#addProjectDialog #baseUrl').val();
      $.ajax({
        type: "POST",
        url: baseUrl + '/projects/create',
        dataType: "json",
        data: {
          name: name
        }
      }).done(function(data) {
        var e = $(
          '<tr id ="project_' + data + '" data-id="' + data + '">' +
          '<td><input type="checkbox" class="checkProject" /></td>' +
          '<td><span class="notyet"></span></td>' +
          '<td><a href="'+ baseUrl + '/tasks/index/' + data + '" class="tasksLink">[タスク]</a>&nbsp;<span class="editProject">[編集]</span>&nbsp;<span class="deleteProject">[削除]</span>&nbsp;<span class="projectDrag">[drag]</span></td>' +
          '</tr>'
        );
		$('#projects').append(e).find('tr:last td:eq(1) span:first-child').text(name);
		$('#addProjectDialog').dialog('close');
      }).fail(function() {
        $('#addProjectDialog').dialog('close');
        alert('通信失敗');
      });
    }
    $(this).removeAttr('disabled');
  });

  //プロジェクトダイアログ(新規追加)オープン
  $('#addProject').click(function() {
    $('#addProjectDialog').dialog('open');
  });

  // プロジェクト(新規、編集)dialog設定
  $('#addProjectDialog, #editProjectDialog').dialog({
    autoOpen: false,
    width: "400px",
    height: "auto",
    show: "drop",
    hide: "drop",
    modal: true,
    close: function() {
      $('#nameErr').remove();
      $('#name').val('');
      $('#id').val('');
    }
  });
});