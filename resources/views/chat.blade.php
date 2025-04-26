@extends('layouts.app')
@section('title', 'Chat')

@section('css')
<style>
  card.chat {
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
<div class="main-content" x-data="chat">
  <section class="section">
    <div class="section-body">
      <div class="row">

        <!-- Users list -->
        <div class="col-md-3">
          <div class="card">
            <div class="body">
              <div id="plist" class="people-list">
                <div class="chat-search">
                  <input type="text" class="form-control" placeholder="Search..." x-model="searchTerm" />
                </div>
                <ul class="chat-list list-unstyled mt-3">
                  <template x-for="(user, index) in filteredUsers" :key="index">
                    <li class="clearfix" :class="{ 'active': selectedUser === user.id }" x-on:click="selectUser(user.id)">
                      <div class="about d-flex justify-content-between w-100 align-items-center">
                        <div>
                          <div class="name d-flex align-items-center">
                            <span x-text="user.name"></span>
                            <template x-if="user.unread > 0">
                              <span class="badge badge-pill badge-success ml-2" x-text="user.unread"></span>
                            </template>
                          </div>
                          <div class="status" x-text="user.id">
                            <i class="material-icons offline">fiber_manual_record</i>
                          </div>
                        </div>
                      </div>
                    </li>
                  </template>
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
                <div class="chat-with" x-text="currentUser ? currentUser.name : 'Select a chat'"></div>
                <div class="chat-num-messages" x-text="currentUser ? `${currentUser.unread} new messages` : ''"></div>
              </div>
            </div>

            <!-- Messages -->
            <div class="chat-messages" id="chat-messages" x-ref="chatBox">
              <template x-for="(message, index) in messages" :key="index">
                <div class="message" :class="message.sender_id === selectedUser ? 'incoming' : 'outgoing'">
                  <span x-text="message.message"></span>
                  <template x-if="message.type === 'image'">
                    <img :src="message.message" class="preview-img" />
                  </template>
                  <template x-if="message.type === 'pdf'">
                    <a>
                      <i class="fas fa-file-pdf"></i>
                      <small x-text="message.message"></small>
                    </a>
                  </template>
                  <div class="message-time" x-text="message.time"></div>
                </div>
              </template>
            </div>

            <div class="chat-form">
              <form method="POST" enctype="multipart/form-data" x-on:submit.prevent="sendMessage">
                <div class="input-group align-items-center">
                  <div class="input-group-prepend">
                    <label class="btn btn-light p-2 m-0" for="file-input" style="cursor: pointer;">
                      <i class="fas fa-paperclip"></i>
                    </label>
                    <input type="file" id="file-input" name="attachment" accept="image/*, application/pdf" class="d-none" x-on:change="handleFileChange" />
                  </div>
                  <input type="text" name="message" id="chat-input" class="form-control" placeholder="Type a message..." 
                         autocomplete="off" x-model="messageText" />
                  <input type="hidden" name="receiver_id" x-model="selectedUser">
                  <input type="hidden" name="type" x-model="chat_type">
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                      <i class="far fa-paper-plane"></i>
                    </button>
                  </div>
                </div>
                <div id="file-preview" x-html="filePreviewHTML"></div>
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
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('chat', () => ({
      users:@json($users),
      messages: [],
      selectedUser: 0,
      messageText: '',
      filePreviewHTML: '',
      selectedFile: null,
      searchTerm: '',
      chat_type: 'text',
      init() {
        this.$nextTick(() => {
          this.scrollToBottom();
          this.currentMessages();
        });
        this.selectedUser = this.users[0].id;
        //this.currentMessages();
      },
      
      get filteredUsers() {
        if (!this.searchTerm.trim()) return this.users;
        const term = this.searchTerm.toLowerCase();
        return this.users.filter(user => 
          user.name.toLowerCase().includes(term)
        );
      },
      
      get currentUser() {
        return this.users.find(user => user.id === this.selectedUser);
      },
      
      currentMessages() {
        $.get(`{{ env('APP_URL')}}api/v2/chat/messages/`+this.selectedUser, (data) => {
          console.log(data);
          const user = this.users.find(u => u.id === this.selectedUser);
          if (user) user.unread = 0;
          this.messages = data;
        });
        // return this.messages[this.selectedUser] || [];
      },
      
      selectUser(userId) {
        this.selectedUser = userId;
        const user = this.users.find(u => u.id === userId);
        if (user) user.unread = 0;
        
        this.$nextTick(() => {
          this.scrollToBottom();
        });
      },
      
      handleFileChange(e) {
        const file = e.target.files[0];
        this.filePreviewHTML = '';
        this.selectedFile = file;
        
        if (!file) return;
        
        if (file.type.startsWith('image/')) {
          const url = URL.createObjectURL(file);
          this.filePreviewHTML = `<img src="${url}" style="max-width: 150px; max-height: 150px; margin-top: 10px; border-radius: 5px;">`;
          this.chat_type = 'image';
        } else if (file.type === 'application/pdf') {
          this.filePreviewHTML = '<i class="fas fa-file-pdf"></i>';
          this.chat_type = 'pdf';
        }
        else{
          alert('Unsupported file type');
          this.selectedFile = null;
        }
      },
      sendMessage() {
        console.log(this.selectedFile);
      },
      scrollToBottom() {
        if (this.$refs.chatBox) {
          this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight;
        }
      }
    }));
  });
</script>
@endsection