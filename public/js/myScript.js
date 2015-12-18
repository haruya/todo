$(function() {

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // flash_messageフェードアウト
  setTimeout(function() {
    $('#flash_message').fadeOut("slow");
  }, 500);

  /********************************************************* プロジェクト */
  // プロジェクトstatus変更処理
  $(document).on('click', '.checkProject', function() {
    var id = $(this).parent().parent().data('id');
    var name = $(this).parent().next().children();
    var baseUrl = $('#baseUrl').val();
    $.ajax({
      type: "POST",
      url: baseUrl + '/projects/status',
      dataType: "json",
      data: {
        id: id
      }
    }).done(function(data) {
      if (name.hasClass('done')) {
        name.removeClass('done').parent().next().children('span:eq(0)').addClass('editProject');
      } else {
        name.addClass('done').parent().next().children('span:eq(0)').removeClass('editProject');
      }
    }).fail(function() {
      alert('通信失敗');
    });
  });

  // プロジェクト並び順変更処理
  $('#projects tbody').sortable({
    axis: 'y',
    opacity: 0.4,
    handle: '.projectDrag',
    update: function() {
      $.ajax({
        type: "POST",
        url: $("#baseUrl").val() + "/projects/sort",
        dataType: "json",
        data: {
          project: $(this).sortable('serialize')
        }
      }).done(function(data) {

      }).fail(function() {
        alert('通信失敗');
      });
    }
  });

  // プロジェクト削除処理
  $(document).on('click', '.deleteProject', function() {
    if (confirm('本当に削除しますか？')) {
      var id = $(this).parent().parent().data('id');
      var baseUrl = $('#baseUrl').val();
      $.ajax({
        type: "POST",
        url: baseUrl + '/projects/delete',
        dataType: "json",
        data: {
          id: id
        }
      }).done(function(data) {
        $('#project_' + data).fadeOut(800, function() {
          alert('プロジェクトを削除しました。');
        });
      }).fail(function() {
        alert('通信失敗');
      });
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
    	var baseUrl = $('#baseUrl').val();
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
          '<td class="text-center"><input type="checkbox" class="checkProject" /></td>' +
          '<td><span class="notyet"></span></td>' +
          '<td class="text-center"><a href="'+ baseUrl + '/tasks/index/' + data + '" class="tasksLink">[タスク]</a>&nbsp;<span class="editProject">[編集]</span>&nbsp;<span class="deleteProject">[削除]</span>&nbsp;<span class="projectDrag">[drag]</span></td>' +
          '</tr>'
        );
		$('#projects').append(e).find('tr:last td:eq(1) span:first-child').text(name);
		$('#addProjectDialog').dialog('close');
		alert('プロジェクトを新規追加しました。');
      }).fail(function() {
        $('#addProjectDialog').dialog('close');
        alert('通信失敗');
      });
    }
    $(this).removeAttr('disabled');
  });

  // プロジェクト編集処理
  $('#editProjectSubmit').click(function() {
    $(this).attr('disabled', 'disabled');
    $('#nameErr').remove();
    var error = false;
    var id = $('#editProjectDialog #id').val();
    var name = $('#editProjectDialog #name').val();
    if (name.length == 0) {
      $('#editProjectDialog #name').parent().after('<p id="nameErr" class="alert alert-danger">プロジェクト名は入力必須です。</p>');
      error = true;
    } else if (name.length > 64) {
      $('#editProjectDialog #name').parent().after('<p id="nameErr" class="alert alert-danger">プロジェクト名は64文字以内で入力してください。</p>');
      error = true;
    }
    if (error == false) {
      var baseUrl = $('#baseUrl').val();
      $.ajax({
        type: "POST",
        url: baseUrl + '/projects/update',
        dataType: "json",
        data: {
          id: id,
          name: name
        }
      }).done(function(data) {
        $('#project_' + id).append().find('td:eq(1) span:first-child').text(name);
        $('#editProjectDialog').dialog('close');
        alert('プロジェクトを編集しました。');
      }).fail(function() {
        $('#editProjectDialog').dialog('close');
        alert('通信失敗');
      });
    }
    $(this).removeAttr('disabled');
  });

  //プロジェクトダイアログ(新規追加)オープン
  $('#addProject').click(function() {
    $('#addProjectDialog').dialog('open');
  });

  // プロジェクトダイアログ(編集)オープン
  $(document).on('click', '.editProject', function() {
    var id = $(this).parent().parent().data('id');
    var name = $(this).parent().prev().children('span:first-child').text();
    $('#editProjectDialog #name').val(name);
    $('#editProjectDialog #id').val(id);
    $('#editProjectDialog').dialog('open');
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

  /********************************************************* タスク */
  // タスクステータス変更処理
  $(document).on('change', '.status', function() {
    var status = $(this).val();
    var id = $(this).parent().parent().data('id');
    var baseUrl = $('#baseUrl').val();
    $.ajax({
      type: "POST",
      url: baseUrl + '/tasks/status',
      dataType: "json",
      data: {
        status: status,
        id: id
      }
    }).done(function(data) {
      $('#task_' + data).removeClass().addClass(status);
    }).fail(function() {
      alert('通信失敗');
    });
  });

  // タスク作業者変更処理
  $(document).on('change', '.worker', function() {
    var worker = $(this).val();
    var id = $(this).parent().parent().data('id');
    var baseUrl = $('#baseUrl').val();
    $.ajax({
      type: "POST",
      url: baseUrl + '/tasks/worker',
      dataType: "json",
      data: {
        worker: worker,
        id: id
      }
    }).done(function(data) {
      if (data === true) {
        // alert('作業者を変更しました。');
      } else {
        alert(data);
      }
    }).fail(function() {
      alert('通信失敗');
    });
  });

  // タスク優先度変更処理
  $(document).on('change', '.priority', function() {
    var priority = $(this).val();
    var id = $(this).parent().parent().data('id');
    var baseUrl = $('#baseUrl').val();
    $.ajax({
      type: "POST",
      url: baseUrl + '/tasks/priority',
      dataType: "json",
      data: {
        priority: priority,
        id: id
      }
    }).done(function(data) {
      // alert('優先度を変更しました。');
    }).fail(function() {
      alert('通信失敗');
    });
  });

  // タスクタイトル変更処理
  $(document).on('change', '.title', function() {
    var title = $(this).val();
    var id = $(this).parent().parent().data('id');
    var baseUrl = $('#baseUrl').val();
    $.ajax({
      type: "POST",
      url: baseUrl + '/tasks/title',
      dataType: "json",
      data: {
        title: title,
        id: id
      }
    }).done(function(data) {
      if (data === true) {
        // alert('タイトルを変更しました。');
      } else {
        alert(data);
      }
    }).fail(function() {
      alert('通信失敗');
    });
  });

  // タスク内容変更処理
  $(document).on('change', '.content', function() {
    var content = $(this).val();
    var id = $(this).parent().parent().data('id');
    var baseUrl = $('#baseUrl').val();
    $.ajax({
      type: "POST",
      url: baseUrl + '/tasks/content',
      dataType: "json",
      data: {
        content: content,
        id: id
      }
    }).done(function(data) {
      // alert('内容を変更しました。');
    }).fail(function() {
      alert('通信失敗');
    });
  });

  // タスク備考変更処理
  $(document).on('change', '.remarks', function() {
    var remarks = $(this).val();
    var id = $(this).parent().parent().data('id');
    var baseUrl = $('#baseUrl').val();
    $.ajax({
      type: "POST",
      url: baseUrl + '/tasks/remarks',
      dataType: "json",
      data: {
        remarks: remarks,
        id: id
      }
    }).done(function(data) {
      // alert('備考を変更しました。');
    }).fail(function() {
      alert('通信失敗');
    });
  });

  // タスク並び順変更処理
  $('#tasks tbody').sortable({
    axis: 'y',
    opacity: 0.4,
    handle: '.taskDrag',
    update:function() {
      $.ajax({
        type: "POST",
        url: $('#baseUrl').val() + "/tasks/sort",
        dataType: "json",
        data: {
          task: $(this).sortable('serialize')
        }
      }).done(function(data) {

      }).fail(function() {
        alert('通信失敗');
      });
    }
  });

  // タスク削除処理
  $(document).on('click', '.deleteTask', function() {
    if (confirm('本当に削除しますか？')) {
      var id = $(this).parent().parent().data('id');
      var baseUrl = $('#baseUrl').val();
      $.ajax({
        type: "POST",
        url: baseUrl + '/tasks/delete',
        dataType: "json",
        data: {
          id: id
        }
      }).done(function(data) {
        $('#task_' + data).fadeOut(800, function() {
          alert('タスクを削除しました。');
        });
      }).fail(function() {
        alert('通信失敗');
      });
    }
  });

  // タスク新規追加処理
  $('#addTask').click(function() {
    $(this).attr('disabled', 'disabled');
    if (confirm('タスクを追加してもよろしいですか？')) {
      var baseUrl = $('#baseUrl').val();
      var projectId = $('#projectId').val();
      $.ajax({
        type: "POST",
        url: baseUrl + '/tasks/create',
        dataType: "json",
        data: {
          project_id: projectId
        }
      }).done(function(data) {
        var created = data["created_at"].substr(0, 10);
        var e = $(
          '<tr id="task_' + data["id"] + '" data-id="' + data["id"] + '">' +
          '<td class="text-center"><select name="statu" class="status">' +
          '<option value="before_work">作業前</option>' +
          '<option value="working">作業中</option>' +
          '<option value="after_work">作業後</option>' +
          '</select></td>' +
          '<td class="text-center">' + created + '</td>' +
          '<td><input type="text" class="worker" value="" /></td>' +
          '<td class="text-center"><select name="priority" class="priority">' +
          '<option value="10">低</option>' +
          '<option value="20">中</option>' +
          '<option value="30">高</option>' +
          '</select></td>' +
          '<td><input type="text" class="title" value="" /></td>' +
          '<td><textarea class="content"></textarea></td>' +
          '<td><textarea class="remarks"></textarea></td>' +
          '<td class="text-center">' +
          '<span class="deleteTask">[削除]</span>' +
          '<span class="taskDrag">[drag]</span></td>' +
          '</tr>'
        );
        $('#tasks').append(e);
        alert('タスクを新規追加しました。');
      }).fail(function() {
        alert('通信失敗');
      });
    }
    $(this).removeAttr('disabled');
  });
});
