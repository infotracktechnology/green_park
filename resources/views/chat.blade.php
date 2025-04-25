@extends('layouts.app')
@section('title', 'Chat')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">

<!-- General CSS Files -->
<link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/izitoast/css/iziToast.min.css') }}">

<!-- Template CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">

<!-- Custom style CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/chat.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/flatpickr-airbnb.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/flatpickr-material.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/flatpickr-custom.css') }}">

<style>
/* Chat container */
.card.chat {
  display: flex;
  flex-direction: column;
  height: 600px;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

/* Chat Header */
.chat-header {
  display: flex;
  align-items: center;
  padding: 15px 20px;
  background: #fff;
  border-bottom: 1px solid #e0e0e0;
}
.chat-header img {
  border-radius: 50%;
  width: 48px;
  height: 48px;
  margin-right: 15px;
}
.chat-about .chat-with {
  font-weight: 600;
  font-size: 18px;
  margin-bottom: 4px;
}
.chat-about .chat-num-messages {
  font-size: 13px;
  color: #999;
}

/* Chat Messages */
.chat-messages {
  flex: 1;
  padding: 20px;
  background: #f9f9f9;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.message {
  max-width: 70%;
  padding: 10px 15px;
  border-radius: 10px;
  position: relative;
  word-break: break-word;
  font-size: 14px;
  line-height: 1.4;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.incoming {
  background: #ffffff;
  align-self: flex-start;
}
.outgoing {
  background: #6371f0;
  color: white;
  align-self: flex-end;
}
.message-time {
  font-size: 10px;
  color: #fffdfd;
  margin-top: 4px;
  text-align: right;
}
/* Preview image before sending */
#file-preview img {
  max-width: 150px;
  max-height: 150px;
  margin-top: 10px;
  border-radius: 5px;
}

/* Sent image in chat bubble */
.message img.preview-img {
  max-width: 150px;
  max-height: 150px;
  margin-top: 8px;
  border-radius: 10px;
  display: block;
}

/* Input Area */
.chat-form {
  padding: 12px 20px;
  background: #fff;
  border-top: 1px solid #e0e0e0;
}
.chat-form .form-control {
 
  padding: 10px 15px;
}
.chat-form .btn {
  border-radius: 50%;
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #6371f0;
  color: white;
}

/* Users list */
.people-list {
  background: #fff;
  border-radius: 15px;
  padding: 15px;
}
.people-list .chat-search input {
  border-radius: 20px;
  padding: 8px 15px;
  border: 1px solid #ddd;
}
.people-list ul.chat-list {
  margin-top: 15px;
}
.people-list ul.chat-list li {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
  padding: 10px;
  border-radius: 12px;
  transition: 0.2s;
  cursor: pointer;
}
.people-list ul.chat-list li.active,
.people-list ul.chat-list li:hover {
  background-color: #f1f3ff;
}
.people-list ul.chat-list li img {
  border-radius: 10px;
  width: 40px;
  height: 40px;
  margin-right: 12px;
}
.people-list ul.chat-list li .about .name {
  font-weight: 600;
}
.people-list ul.chat-list li .about .status {
  font-size: 12px;
  color: gray;
}
.people-list ul.chat-list li .about .status i {
  font-size: 10px;
  margin-right: 5px;
}

</style>
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">

        <!-- Users list -->
        <div class="col-md-3">
          <div class="card">
            <div class="body">
              <div id="plist" class="people-list">
                <div class="chat-search">
                  <input type="text" class="form-control" placeholder="Search..." />
                </div>
                <ul class="chat-list list-unstyled mt-3">
                 
                    <li class="clearfix active">
                        <img src="{{ asset('img/users/user-3.png') }}" alt="avatar">
                        <div class="about d-flex justify-content-between w-100 align-items-center">
                          <div>
                            <div class="name d-flex align-items-center">
                              Maria Smith
                              <span class="badge badge-pill badge-success ml-2">2</span>
                            </div>
                            <div class="status">
                              <i class="material-icons offline">fiber_manual_record</i> left 7 mins ago
                            </div>
                          </div>
                        </div>
                      </li>
                      
                  <!-- Repeat for other users -->
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Chat box -->
        <div class="col-md-9">
          <div class="card chat">
            <!-- Header -->
            <div class="chat-header clearfix">
             
              <div class="chat-about">
                <div class="chat-with">Maria Smith</div>
                <div class="chat-num-messages">2 new messages</div>
              </div>
            </div>

            <!-- Messages -->
            <div class="chat-messages" id="chat-messages">
              <!-- Example: incoming -->
              <div class="message incoming">
                Hi there! How are you?
                <div class="message-time">09:42 AM</div>
              </div>
              <!-- Example: outgoing -->
              <div class="message outgoing">
                I'm good, thanks! You?
                <div class="message-time">09:43 AM</div>
              </div>
            </div>

            <div class="chat-form">
              <form id="chat-form" enctype="multipart/form-data">
                <div class="input-group align-items-center">
                  <div class="input-group-prepend">
                    <label class="btn btn-light p-2 m-0" for="file-input" style="cursor: pointer;">
                      <i class="fas fa-paperclip"></i>
                    </label>
                    <input type="file" id="file-input" name="attachment" class="d-none" />
                  </div>
                  <input type="text" name="message" id="chat-input" class="form-control" placeholder="Type a message..." autocomplete="off " />
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                      <i class="far fa-paper-plane"></i>
                    </button>
                  </div>
                </div>
                <div id="file-preview"></div>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.1/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/confirmDate/confirmDate.js"></script>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
<script src="{{ asset('assets/js/scripts.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>

<script>
    const chatBox = document.getElementById('chat-messages');
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
    
    const fileInput = document.getElementById('file-input');
    const filePreview = document.getElementById('file-preview');
    fileInput.addEventListener('change', e => {
      const file = e.target.files[0];
      filePreview.innerHTML = '';
      if (!file) return;
      if (file.type.startsWith('image/')) {
        const img = new Image();
        img.src = URL.createObjectURL(file);
        filePreview.appendChild(img);
      } else if (file.type === 'application/pdf') {
        filePreview.innerHTML = '<i class="fas fa-file-pdf"></i>';
      }
      filePreview.appendChild(document.createElement('div')).innerText = `Attached: ${file.name}`;
    });
    
    document.getElementById('chat-form').addEventListener('submit', e => {
      e.preventDefault();
      const input = document.getElementById('chat-input');
      const file = fileInput.files[0];
      const text = input.value.trim();
      if (!text && !file) return;
    
      const msg = document.createElement('div');
      msg.className = 'message outgoing';
      msg.innerHTML = `
        ${text || ''}
        ${file?.type?.startsWith('image/') ? `<img src="${URL.createObjectURL(file)}" class="preview-img" />` : ''}
        ${file?.type === 'application/pdf' ? `<i class="fas fa-file-pdf"></i>` : ''}
        ${file ? `<div class="message-time"><small>${file.name}</small></div>` : ''}
        <div class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
      `;
      chatBox.appendChild(msg);
      chatBox.scrollTop = chatBox.scrollHeight;
      e.target.reset();
      filePreview.innerHTML = '';
    });
    </script>
    
@endsection
