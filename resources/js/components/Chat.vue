<template>
    <div class="flex h-full">
        <ChatUserList
            :current-user="currentUser"
            :chat-with="chatWith"
            @updatedChatWith="updateChatWith"
        >
        </ChatUserList>

        <div v-if="chatWith" class="w-4/5 flex flex-col">
            <ChatArea
                :chat-id="chatWith"
                :messages="messages"
            >
            </ChatArea>
            <div class="flex-initial p-2">
                <input
                    class="border-2 border-solid rounded border-gray-600 w-full p-3"
                    type="text"
                    v-model="text"
                    @keyup.enter="submit"
                >
            </div>
        </div>
        <div v-else class="p-3">
            채팅 상대를 선택해주세요.
        </div>
    </div>
</template>

<script>
    import ChatUserList from "./ChatUserList";
    import ChatArea from "./ChatArea";

    export default {
        props: {
            currentUser: {
                type: Number,
                required: true
            }
        },
        components: {
            ChatUserList,
            ChatArea
        },
        data() {
            return {
                chatWith: null,
                text: '',
                messages: []
            }
        },
        created() {
            window.Echo.private('chats').listen('MessageSent', e => {
                console.log(e);
                if (e.message.to.id === this.currentUser && e.message.from.id === this.chatWith) {
                    this.messages.push(e.message)
                }
            });
        },
        methods: {
            updateChatWith(value) {
                this.chatWith = value;
                this.getMessages();
            },
            getMessages() {
                axios.get('/api/messages', {
                    params: {
                        to: this.chatWith,
                        form: this.currentUser
                    }
                }).then(res => {
                    this.messages = res.data.messages;
                });
            },
            submit() {
                if (this.text) {
                    axios.post('/api/messages', {
                        text: this.text,
                        to: this.chatWith,
                        form: this.currentUser
                    }).then(res => {
                        console.log(res.data.message);
                        this.messages.push(res.data.message);
                    });
                }
                this.text = '';
            }
        }
    }
</script>
