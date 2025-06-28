import {Controller} from '@hotwired/stimulus';
import {Message} from "@/Message";
import {ConversationService} from "@/service/ConversationService";

/**
 * This controller catches Mercure event if message is received and shows it
 */
export default class MercureMessageController extends Controller {

    static values = {
        mercureUrl: String
    }
    declare mercureUrlValue: string
    private conversationDiv!: HTMLDivElement;
    private conversationService: any;
    private eventSource!: EventSource;

    connect() {

        this.conversationDiv = document.querySelector('#conversation') as HTMLDivElement
        this.conversationService = new ConversationService(this.conversationDiv)

        this.loadMessages()
        this.conversationService.scrollToBottom()

        /**
         * Declare mercure EventSource
         * @type {EventSource}
         */
        this.eventSource = new EventSource(this.mercureUrlValue, {
            withCredentials: true
        });

        this.listenToMercure()

        document.addEventListener('click', this.refreshListening)

    }


    /**
     * Refresh conversation and awake mercure eventSource if closed
     */
    refreshListening = () => {
        if (this.eventSource.readyState === EventSource.CLOSED) {
            this.loadMessages().then(() => {
                this.listenToMercure()
            })
        }
    }

    /**
     * Listening to mercure
     */
    listenToMercure = () => {

        this.eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data)
            this.conversationService.appendMessage(data)
            this.conversationService.scrollToBottom()
        };

        this.eventSource.onerror = (error) => {
            console.error('Erreur avec Mercure', error)
        };

    }

    /**
     * Get messages list and build html
     * @returns {Promise<void>}
     */
    loadMessages = async () => {
        const response = await fetch(`/messages-with-as-json/${this.conversationDiv.dataset.conversationWith}`, {
            method: 'GET'
        })
        const data = await response.json()
        await this.conversationService.buildConversationHtml(data.conversation)

    }

    disconnect() {
        this.eventSource?.close();
        document.removeEventListener('click', this.refreshListening)
    }
}
