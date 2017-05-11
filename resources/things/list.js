function updateButtons() {
  var items = document.getElementsByClassName('entry');
  for (var i = 0; i < items.length; ++i) {
    Button.initialize(items[i], function() {
      
    });
  }
}

function getLastDispOrder() {
  var last = document.getElementById('list').lastElementChild;
  return parseInt(last.getAttribute('data-disp-order'), 30);
}


function loadNext() {
  if (this.classList.contains('disabled')) {
    return;
  }

  this.classList.add('disabled');

  var originalText = this.innerHTML;
  this.innerHTML = this.getAttribute('data-loading-msg');

  var offset = getLastDispOrder() - 1;
  var url = URL_BASE + '/announce/partial?disp_faulty=' + DISP_FAULTY + '&offset=' + offset;

  var xhr = new XMLHttpRequest();
  xhr.addEventListener('readystatechange', function() {
    if (xhr.readyState != 4) {
      return;
    }

    if (xhr.status == 200 && !xhr.responseText) {
      // これ以上お知らせがない
      this.innerHTML = this.getAttribute('data-no-more-msg');;
    } else if (xhr.status == 200 && xhr.responseText) {
      // お知らせが取得できた
      document.getElementById('list').innerHTML += xhr.responseText;
      this.innerHTML = originalText;
      this.classList.remove('disabled');
      updateButtons();
      Ps.update(document.getElementById('container'));
    } else {
      // エラー
      this.innerHTML = originalText;
      this.classList.remove('disabled');
    }
  }.bind(this));

  xhr.open('GET', url);
  xhr.setRequestHeader('User-Id', USER_ID);
  xhr.setRequestHeader('Authorize', AUTHORIZE_DATA);
  xhr.send();
}