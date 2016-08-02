'use strict'

var doc = document;
var win = window;

var ajax = require('./ajax.js');
var parent = require('./utils/parent.js');
var alertMsg = require('./alertMsg.js');

exports.init = function(btn){
  btn.addEventListener('click', deleteExCB);
}

function deleteExCB(evt){
  evt.preventDefault();
  
  var result = confirm("Advarsel. Elementet slettes permanent.");
  if(result !== true){
    return;
  }
  
  var parentRow = parent(this, 'tr, .item');
  if(parentRow == null){
    console.log('no wrapper defined!');
    return;
  }

  var url = this.getAttribute('data-url');
  var id = this.getAttribute('data-id');
  parentRow.className = 'opaque';
  
  ajax.post(url, 'id='+id, deleteItemCB); 
  
  function deleteItemCB(status, data){
    data = JSON.parse(data);
    if (data.success){
      parentRow.remove();
    }
    else{
      parentRow.classList.remove('opaque');
      alertMsg.init(data.msg, doc.querySelector('.table.table-striped'));
    }
  }
}