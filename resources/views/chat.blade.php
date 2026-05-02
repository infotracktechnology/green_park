@extends('layouts.app')
@section('title', 'Chat')

@section('css')
<style>
  :root {
    --primary: #6371f0;
    --primary-hover: #525fd4;
    --bg-chat: #f0f2f5;
    --bg-incoming: #ffffff;
    --text-incoming: #1a1a1a;
    --text-outgoing: #ffffff;
    --border-light: #e5e7eb;
    --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
    --radius: 12px;
  }

  .chat-container {
    display: flex;
    gap: 16px;
    height: calc(100vh - 140px);
    min-height: 500px;
  }

  /* Sidebar */
  .chat-sidebar {
    width: 300px;
    flex-shrink: 0;
    background: #fff;
    border-radius: var(--radius);
    box-shadow: var(--shadow-md);
    display: flex;
    flex-direction: column;
  }

  .chat-search {
    padding: 12px;
    border-bottom: 1px solid var(--border-light);
  }

  .chat-search input {
    border-radius: 20px;
    padding: 8px 16px;
    border: 1px solid var(--border-light);
    transition: all 0.2s;
  }

  .chat-search input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99,113,240,0.1);
    outline: none;
  }

  .user-list {
    flex: 1;
    overflow-y: auto;
    list-style: none;
    margin: 0;
    padding: 8px;
  }

  .user-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.15s;
  }

  .user-item:hover, .user-item.active {
    background: #f3f4f6;
  }

  .user-item.active {
    background: #eef2ff;
  }

  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
    flex-shrink: 0;
  }

  .user-info {
    flex: 1;
    min-width: 0;
  }

  .user-name {
    font-weight: 500;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .user-status {
    font-size: 12px;
    color: #6b7280;
  }

  .badge-unread {
    background: var(--primary);
    color: #fff;
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: 500;
  }

  /* Main Chat */
  .chat-main {
    flex: 1;
    background: #fff;
    border-radius: var(--radius);
    box-shadow: var(--shadow-md);
    display: flex;
    flex-direction: column;
  }

  .chat-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .chat-header-info h6 {
    margin: 0;
    font-weight: 600;
  }

  .chat-header-info small {
    color: #6b7280;
    font-size: 12px;
  }

  .chat-messages {
    flex: 1;
    padding: 20px;
    background: var(--bg-chat);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .message {
    max-width: 65%;
    padding: 10px 14px;
    border-radius: var(--radius);
    word-break: break-word;
    font-size: 14px;
    line-height: 1.4;
    animation: fadeIn 0.2s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .message.incoming {
    background: var(--bg-incoming);
    color: var(--text-incoming);
    align-self: flex-start;
    border-bottom-left-radius: 4px;
    box-shadow: var(--shadow-sm);
  }

  .message.outgoing {
    background: var(--primary);
    color: var(--text-outgoing);
    align-self: flex-end;
    border-bottom-right-radius: 4px;
  }

  .message-time {
    font-size: 10px;
    opacity: 0.7;
    margin-top: 4px;
    text-align: right;
  }

  .message img.preview-img {
    max-width: 200px;
    max-height: 200px;
    margin-top: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
  }

  .message img.preview-img:hover {
    transform: scale(1.02);
  }

  .message a.file-link {
    color: inherit;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    text-decoration: none;
  }

  .message.outgoing a.file-link {
    color: #fff;
  }

  .empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    text-align: center;
    padding: 40px;
  }

  .empty-state i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
  }

  /* Input Area */
  .chat-form {
    padding: 16px 20px;
    border-top: 1px solid var(--border-light);
  }

  .chat-form .form-control {
    border-radius: 20px;
    padding: 10px 16px;
    border: 1px solid var(--border-light);
  }

  .chat-form .form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99,113,240,0.1);
  }

  .btn-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-icon:hover {
    transform: scale(1.05);
  }

  .btn-send {
    background: var(--primary);
    color: #fff;
  }

  .btn-send:hover {
    background: var(--primary-hover);
  }

  .btn-attach {
    background: #f3f4f6;
    color: #6b7280;
  }

  .btn-attach:hover {
    background: #e5e7eb;
  }

  .file-preview {
    margin-top: 8px;
    padding: 8px 12px;
    background: #f9fafb;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
  }

  .file-preview img {
    max-height: 60px;
    border-radius: 6px;
  }

  .file-preview .btn-remove {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    padding: 4px;
  }

  /* Scrollbar */
  .chat-messages::-webkit-scrollbar,
  .user-list::-webkit-scrollbar {
    width: 6px;
  }

  .chat-messages::-webkit-scrollbar-thumb,
  .user-list::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
  }
</style>
@endsection

@section('main')
<div class="main-content" x-data="chatApp" x-init="init">
  <section class="section">
    <div class="section-body">
      <div class="chat-container">

        <!-- Users Sidebar -->
        <div class="chat-sidebar">
          <div class="chat-search">
            <input type="text" class="form-control" placeholder="Search users..." x-model.debounce.300ms="searchTerm" />
          </div>
          <ul class="user-list">
            <template x-for="user in filteredUsers" :key="user.id">
              <li class="user-item" 
                  :class="{ 'active': selectedUser === user.id }" 
                  x-on:click="selectUser(user.id)">
                <div class="user-avatar" x-text="user.username?.charAt(0).toUpperCase()"></div>
                <div class="user-info">
                  <div class="user-name" x-text="user.username"></div>
                  <div class="user-status">Online</div>
                </div>
                <span class="badge-unread" x-show="user.unread > 0" x-text="user.unread"></span>
              </li>
            </template>
          </ul>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main">
          <!-- Header -->
          <div class="chat-header" x-show="currentUser">
            <div class="user-avatar" x-text="currentUser?.username?.charAt(0).toUpperCase()"></div>
            <div class="chat-header-info">
              <h6 x-text="currentUser?.username"></h6>
              <small>Online</small>
            </div>
          </div>

          <!-- Messages -->
          <div class="chat-messages" x-ref="chatBox" x-init="$watch('messages', () => scrollToBottom())">
            <template x-if="!selectedUser">
              <div class="empty-state">
                <i class="fas fa-comments"></i>
                <p>Select a user to start chatting</p>
              </div>
            </template>
            <template x-if="selectedUser && messages.length === 0">
              <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No messages yet</p>
              </div>
            </template>
            <template x-for="message in messages" :key="message.id ?? message.time">
              <div class="message" :class="message.sender_id === selectedUser ? 'incoming' : 'outgoing'">
                <template x-if="message.type === 'image' && message.message">
                  <img :src="message.message" class="preview-img" @click="$el.requestFullscreen?.()" />
                </template>
                <template x-if="message.type !== 'image'">
                  <span x-text="message.message"></span>
                </template>
                <template x-if="message.type === 'pdf'">
                  <a :href="message.message" target="_blank" class="file-link">
                    <i class="fas fa-file-pdf"></i>
                    <span x-text="message.file_name || 'document.pdf'"></span>
                  </a>
                </template>
                <div class="message-time" x-text="formatTime(message.time)"></div>
              </div>
            </template>
          </div>

          <!-- Input -->
          <div class="chat-form" x-show="selectedUser">
            <form x-on:submit.prevent="sendMessage" enctype="multipart/form-data">
              <div class="input-group">
                <div class="input-group-prepend">
                  <label class="btn-icon btn-attach" for="file-input">
                    <i class="fas fa-paperclip"></i>
                  </label>
                  <input type="file" id="file-input" class="d-none" accept="image/*,.pdf" x-on:change="handleFileChange" />
                </div>
                <input type="text" 
                       class="form-control" 
                       placeholder="Type a message..." 
                       x-model="messageText"
                       @keydown.enter.prevent="sendMessage"
                       autocomplete="off" />
                <input type="hidden" name="receiver_id" :value="selectedUser">
                <input type="hidden" name="type" :value="chatType">
                <div class="input-group-append">
                  <button type="submit" class="btn-icon btn-send">
                    <i class="fas fa-paper-plane"></i>
                  </button>
                </div>
              </div>
              <template x-if="filePreviewHTML">
                <div class="file-preview" x-html="filePreviewHTML"></div>
              </template>
            </form>
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
    Alpine.data('chatApp', () => ({
      users: @json($users),
      messages: [],
      selectedUser: null,
      messageText: '',
      filePreviewHTML: '',
      selectedFile: null,
      searchTerm: '',
      chatType: 'text',
      refreshInterval: null,

      init() {
        this.selectedUser = this.users[0]?.id ?? null;
        if (this.selectedUser) this.loadMessages();
        this.refreshInterval = setInterval(() => {
          if (this.selectedUser) this.loadMessages();
        }, 5000);
      },

      destroy() {
        if (this.refreshInterval) clearInterval(this.refreshInterval);
      },

      get filteredUsers() {
        if (!this.searchTerm) return this.users;
        const term = this.searchTerm.toLowerCase();
        return this.users.filter(u => u.username?.toLowerCase().includes(term));
      },

      get currentUser() {
        return this.users.find(u => u.id === this.selectedUser);
      },

      loadMessages() {
        fetch(`{{ env('APP_URL') }}api/v2/chat/messages/${this.selectedUser}`)
          .then(res => res.json())
          .then(data => {
            this.messages = data;
            const user = this.users.find(u => u.id === this.selectedUser);
            if (user) user.unread = 0;
          })
          .catch(err => console.error('Failed to load messages:', err));
      },

      selectUser(userId) {
        this.selectedUser = userId;
        this.messages = [];
        this.loadMessages();
      },

      handleFileChange(e) {
        const file = e.target.files[0];
        if (!file) return;

        this.selectedFile = file;
        this.filePreviewHTML = '';

        if (file.type.startsWith('image/')) {
          const url = URL.createObjectURL(file);
          this.filePreviewHTML = `
            <img src="${url}" alt="preview">
            <span>${file.name}</span>
            <button type="button" class="btn-remove" @click="clearFile()">
              <i class="fas fa-times"></i>
            </button>`;
          this.chatType = 'image';
        } else if (file.type === 'application/pdf') {
          this.filePreviewHTML = `
            <i class="fas fa-file-pdf text-danger mr-2"></i>
            <span>${file.name}</span>
            <button type="button" class="btn-remove" @click="clearFile()">
              <i class="fas fa-times"></i>
            </button>`;
          this.chatType = 'pdf';
        } else {
          alert('Only images and PDF files are supported');
          this.clearFile();
        }
      },

      clearFile() {
        this.selectedFile = null;
        this.filePreviewHTML = '';
        this.chatType = 'text';
        document.getElementById('file-input').value = '';
      },

      sendMessage() {
        if (!this.messageText.trim() && !this.selectedFile) return;

        const formData = new FormData();
        formData.append('message', this.messageText);
        formData.append('receiver_id', this.selectedUser);
        formData.append('type', this.chatType);
        if (this.selectedFile) formData.append('attachment', this.selectedFile);

        fetch(`{{ env('APP_URL') }}api/v2/chat/messages`, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
          body: formData
        })
        .then(res => res.json())
        .then(() => {
          this.messageText = '';
          this.clearFile();
          this.loadMessages();
        })
        .catch(err => console.error('Failed to send message:', err));
      },

      formatTime(time) {
        if (!time) return '';
        const date = new Date(time);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      },

      scrollToBottom() {
        this.$nextTick(() => {
          if (this.$refs.chatBox) {
            this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight;
          }
        });
      }
    }));
  });
</script>
@endsection