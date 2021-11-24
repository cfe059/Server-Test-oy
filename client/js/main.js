const url = 'http://localhost:8080/api/page';

function format(id, title, message) {
  let card = '';
  card += '<li id="post-' + id + '" class="card" data-id="' + id + '">';
  card += '<div class="left">' + message + '</div>';
  card += '<div class="right"><div class="button-block"><button class="edit">編集</button><button class="delete">削除</button></div><p class="name">' + title + '</p></div>'
  card += '</li>';
  return card;
}

// 一覧取得
$.ajax({
  type: 'GET',
  url: url
}).done(function(data){
  data = JSON.parse(data)
  data.forEach(function(post) {
    $('#lists').append(format(post[0], post[1], post[2]));
  });
});

$('#post').on('click', () => {
  let title = $('#title').val();
  let message = $('#message').val();

  if(title != "" && message != ""){
  $.ajax({
    type: 'POST',
    url: url,
    data: {
        "title": title,
        "message": message
    }
  }).done(function(data){
    window.location.reload();
  });
}
});


$('.lists').on('click', '.edit', function() {
  // 投稿id、タイトルとメッセージを取得
  let $id = $(this).parents('.card').data('id');
  let message = $(this).parents('.right').prev().text();
  let title = $(this).parents('.button-block').next().text()

  // タイトルとメッセージを格納
  $('#title').val(title);
  $('#message').val(message);

  // ボタンテキストを「編集」に変更
  // classに'update'を追加
  $('#post').text('更新');
  $('#post').addClass('update');

  // 更新を押したら、更新APIを叩く
  $('.update').on('click', function() {
    // 更新APIを叩く
    $.ajax({
      type: 'PUT',
      url: url,
      data: {
        "id": $id,
        "title": title,
        "message": message
      }
    }).done(function(data){
      alert('更新完了しました。');
      window.location.reload();
    });
  });
});

$('.lists').on('click', '.delete', function() {
  // 投稿idを取得
  let $id = $(this).parents('.card').data('id');

  // アラートを出す
  let result = window.confirm('本当に削除してよいですか？');

  if (result) {
    // 投稿idをもとに削除APIを叩く
    $.ajax({
      type: 'POST',
      url: url + '/delete',
      data: {
        "id": $id
      }
    }).done(function(data){
      alert('削除完了しました。');
      window.location.reload();
    });
  }

});

