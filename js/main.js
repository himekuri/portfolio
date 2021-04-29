/**
 * ナビゲーションバーのリンクをクリックしたら、
 * スムーズにスクロールしながら対象位置に移動
 */
$('.nav-link').on('click', (e) => {
  const destination = $(e.target).attr('href');

  // 本来のクリックイベントは処理しない
  e.preventDefault();

  $('html, body').animate(
    {
      scrollTop: $(destination).offset().top,
    },
    1000,
  );

  // ハンバーガーメニューが開いている場合は閉じる
  $('.navbar-toggler:visible').trigger('click');
});

// 見出しが画面中央まで来たら下線を表示する 
$('.heading').waypoint({
  handler(direction) {
    if (direction === 'down') {
      $(this.element).addClass('isActive');
      this.destroy();
    }
  },
  offset: '50%',
});
