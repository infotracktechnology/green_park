@extends('layouts.app')
@section('title', 'Chat')

@section('css')
<style>
  [x-cloak] { display: none !important; }
  .chat-container { height: 75vh; min-height: 500px; }
  .chat-sidebar { width: 320px; }
  .chat-messages::-webkit-scrollbar { width: 6px; }
  .chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
  .message { max-width: 70%; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
  .preview-img { max-width: 100%; border-radius: 8px; margin-top: 8px; cursor: pointer; }
</style>
@endsection

@section('main')
<div class="main-content" x-data="chat" x-cloak>
  <div class="container-fluid py-4">
    <div class="chat-container flex bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-200">
      
      <!-- Sidebar -->
      <aside class="chat-sidebar flex flex-col bg-white border-r border-gray-200">
        <div class="p-5 border-b border-gray-200">
          <h5 class="mb-3 font-bold">Messages</h5>
          <input type="text" class="form-control form-control-sm rounded-full" placeholder="Search users..." x-model="searchTerm">
        </div>
        
        <ul class="flex-1 overflow-y-auto list-none p-0 m-0">
          <template x-for="user in filteredUsers" :key="user.id">
            <li class="flex items-center p-4 cursor-pointer hover:bg-gray-50 border-b border-gray-100 transition-colors"
                :class="{ 'bg-indigo-50 border-l-4 border-l-indigo-500': selectedUser === user.id }"
                @click="selectUser(user.id)">
              <div class="w-11 h-11 bg-gray-200 rounded-full mr-3 flex items-center justify-center font-bold text-gray-500" x-text="user.name.charAt(0)"></div>
              <div class="flex-1 overflow-hidden">
                <div class="flex justify-between items-center">
                  <span class="font-bold truncate" x-text="user.name"></span>
                  <small x-show="user.unread > 0" class="badge badge-pill badge-primary" x-text="user.unread"></small>
                </div>
                <small class="text-muted truncate d-block" x-text="'ID: ' + user.id"></small>
              </div>
            </li>
          </template>
        </ul>
      </aside>

      <!-- Main Chat -->
      <main class="flex-1 flex flex-col bg-slate-50 relative">
        
        <!-- Header -->
        <header class="px-6 py-4 bg-white border-b border-gray-200 flex items-center justify-between">
          <template x-if="currentUser">
            <div>
              <h6 class="mb-0 font-bold" x-text="currentUser.name"></h6>
              <small class="text-success">Online</small>
            </div>
          </template>
          <template x-if="currentUser">
            <span class="badge badge-light text-muted" x-text="currentUser.unread + ' unread'"></span>
          </template>
        </header>

        <!-- Messages -->
        <div class="chat-messages flex-1 p-6 overflow-y-auto flex flex-col gap-4 scrollbar-thin" x-ref="chatBox">
          <template x-if="!selectedUser">
            <div class="flex flex-col items-center justify-center h-full text-gray-500 text-center">
              <i class="fas fa-comments text-5xl mb-4 opacity-30"></i>
              <p>Select a conversation to start chatting</p>
            </div>
          </template>

          <template x-for="(msg, index) in messages" :key="index">
            <div class="message px-4 py-3 text-sm leading-relaxed relative"
                 :class="msg.sender_id === selectedUser 
                   ? 'self-start bg-white text-gray-800 rounded-r-2xl rounded-bl-2xl rounded-tl-sm border border-gray-200' 
                   : 'self-end bg-indigo-500 text-white rounded-l-2xl rounded-br-2xl rounded-tr-sm'">
              
              <span x-text="msg.message" x-show="msg.type === 'text'"></span>
              <img x-show="msg.type === 'image'" :src="msg.message" class="preview-img" @click="window.open(msg.message, '_blank')" />
              <a x-show="msg.type === 'pdf'" :href="msg.message" target="_blank" class="btn btn-sm btn-light text-danger mt-2">
                <i class="fas fa-file-pdf mr-1"></i> View Document
              </a>

              <span class="block text-right text-xs mt-1 opacity-70" 
                    :class="msg.sender_id === selectedUser ? 'text-gray-500' : 'text-indigo-100'"
                    x-text="msg.time"></span>
            </div>
          </template>
        </div>

        <!-- Input -->
        <footer class="p-5 bg-white border-t border-gray-200">
          <div x-show="selectedFile" class="bg-slate-100 rounded-xl px-4 py-2 mb-3 flex items-center">
            <img x-show="chat_type === 'image'" :src="previewUrl" class="h-10 w-10 object-cover rounded mr-3">
            <i x-show="chat_type === 'pdf'" class="fas fa-file-pdf text-danger mr-3 fa-lg"></i>
            <span class="small text-truncate flex-1" x-text="selectedFile?.name"></span>
            <button type="button" class="btn btn-sm text-danger" @click="clearFile">
              <i class="fas fa-times"></i>
            </button>
            
          </div>

          <form @submit.prevent="sendMessage">
            <div class="bg-slate-100 rounded-full px-4 py-1 flex items-center border border-gray-200">
              <label class="text-gray-500 p-2 cursor-pointer hover:text-indigo-500 transition-colors m-0">
                <i class="fas fa-paperclip"></i>
                <input type="file" class="d-none" x-ref="fileInput" @change="handleFileChange" accept="image/*,application/pdf">
              </label>
              <input type="text" class="border-0 bg-transparent px-3 py-2 flex-1 focus:outline-none" 
                     placeholder="Write a message..." x-model="messageText" autocomplete="off">
              <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white border-0 w-10 h-10 rounded-full flex items-center justify-center transition-transform hover:scale-105">
                <i class="fas fa-paper-plane"></i>
              </button>
            </div>
          </form>
        </footer>
      </main>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('chat', () => ({
      users: @json($users),
      messages: [],
      selectedUser: null,
      messageText: '',
      selectedFile: null,
      previewUrl: null,
      searchTerm: '',
      chat_type: 'text',

      init() {
        if (this.users.length > 0) {
          this.selectedUser = this.users[0].id;
          this.loadMessages();
        }
      },

      get filteredUsers() {
        return this.users.filter(u => u.name.toLowerCase().includes(this.searchTerm.toLowerCase()));
      },

      get currentUser() {
        return this.users.find(u => u.id === this.selectedUser);
      },

      async loadMessages() {
        if (!this.selectedUser) return;
        try {
          const response = await fetch(`{{ url('api/v2/chat/messages') }}/${this.selectedUser}`);
          if (!response.ok) throw new Error('Failed to load');
          this.messages = await response.json();
          
          const user = this.users.find(u => u.id === this.selectedUser);
          if (user) user.unread = 0;
          
          this.scrollToBottom();
        } catch (err) {
          console.error(err);
        }
      },

      async selectUser(userId) {
        if (this.selectedUser === userId) return;
        this.selectedUser = userId;
        await this.loadMessages();
      },

      handleFileChange(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);

        this.selectedFile = file;
        this.chat_type = file.type.startsWith('image/') ? 'image' : (file.type === 'application/pdf' ? 'pdf' : 'text');

        if (this.chat_type === 'image') {
          this.previewUrl = URL.createObjectURL(file);
        }
      },

      clearFile() {
        if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
        this.selectedFile = null;
        this.previewUrl = null;
        this.chat_type = 'text';
        this.$refs.fileInput && (this.$refs.fileInput.value = '');
      },

      async sendMessage() {
        if (!this.messageText.trim() && !this.selectedFile) return;

        const formData = new FormData();
        formData.append('receiver_id', this.selectedUser);
        formData.append('message', this.messageText);
        formData.append('type', this.chat_type);
        if (this.selectedFile) {
          formData.append('attachment', this.selectedFile);
        }

        try {
          const response = await fetch(`{{ url('api/v2/chat/send') }}`, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
          });

          if (response.ok) {
            this.messageText = '';
            this.clearFile();
            await this.loadMessages();
          }
        } catch (err) {
          alert('Failed to send message');
        }
      },

      scrollToBottom() {
        this.$nextTick(() => {
          const el = this.$refs.chatBox;
          el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
        });
      }
    }));
  });
</script>
@endsection