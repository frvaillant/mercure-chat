
import {MessageData} from "@/models/MessageDataInterface";
import {Message} from "@/Message";

/**
 *
 * This service manages conversation elements.
 *
 * It builds conversation for basics data.
 *
 * It can add message element to conversation
 *
 * It manages the scroll of conversation
 */
export class ConversationService {

    private conversationContainer: HTMLDivElement;

    constructor(conversationContainer: HTMLDivElement) {

        this.conversationContainer = conversationContainer
    }

    scrollToBottom = () => {
        this.conversationContainer.scrollTop = this.conversationContainer.scrollHeight
    }

    scrollToTop = () => {
        this.conversationContainer.scrollTop = 0
    }

    appendMessage = (messageData: MessageData) => {
        const message = new Message({
            id: messageData.id,
            text: messageData.text,
            from: messageData.from,
            date: messageData.date,
            class: messageData.class
        })
        this.conversationContainer.appendChild(message.getElement())
    }

    cleanSkeletons = () => {
        const skeletons = this.conversationContainer.querySelectorAll('.message.skeleton')

        if(skeletons.length === 0) {
            return
        }
        skeletons.forEach(skeleton => {
            skeleton.remove()
        })
    }

    buildConversationHtml = async (conversation: object) => {
        Object.values(conversation).forEach(message => {
            const m = new Message(message)
            const mElement = m.getElement()
            this.conversationContainer.appendChild(mElement)
            this.scrollToBottom()
        })
        this.cleanSkeletons()
    }


}
