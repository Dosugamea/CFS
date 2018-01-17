var valid,valid2;
function verify() {
  valid=false;
  var info='';
  var id=document.getElementById('id');
  if(!isNaN(id.value) && parseInt(id.value)>0 && parseInt(id.value)<=999999999)
    valid=true;
  else if(parseInt(id.value)>999999999)
    info='你输入的数太大了！';
  else
    info='请输入一个正整数';
  if(valid) {
    for(var i in exist_id) {
      if(parseInt(id.value)==exist_id[i]) {
        valid=false;
        info='错误：指定的ID('+exist_id[i]+')已被使用';
      }
    }
  }
  if(valid) {
    id.style.backgroundColor='#00FF00';
  } else {
    id.style.backgroundColor='#FF0000';
  }
  document.getElementById('info').innerText=info;
  if(document.getElementById('name').value=='') {
    valid=false;
    document.getElementById('name').style.backgroundColor='#FF0000';
  } else document.getElementById('name').style.backgroundColor='#00FF00';
  verify3();
}
function verify2() {
  verify();
  valid2=true;
  var info='';
  var t1=document.getElementById('pass1');
  var t2=document.getElementById('pass2');
  if(t1.value=='') {
    valid2=false;
    t1.style.backgroundColor='#FF0000';
    info='请输入密码'
  } else {
    t1.style.backgroundColor='#00FF00';
  }
  if(t1.value!=t2.value && t1.value!='') {
    valid2=false;
    t2.style.backgroundColor='#FF0000';
    info='两次输入的密码不一致'
  } else if(t1.value=='') {
    t2.style.backgroundColor='#FF0000';
  } else {
    t2.style.backgroundColor='#00FF00';
  }
  document.getElementById('info2').innerText=info;
  verify3();
}
function verify3() {
  if(valid && valid2) {
    document.getElementById('submit').disabled=false;
  } else {
    document.getElementById('submit').disabled=true;
  }
}