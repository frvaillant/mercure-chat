import { Controller } from '@hotwired/stimulus';
import { TypewriterService } from "@/service/TypewriterService";

/**
 * This controller catches the typing mercure event and shows html while user is writing
 */
export default class MercureTypingController extends Controller<HTMLTextAreaElement> {

    static values = {
        mercureUrl: String,
    }
    declare readonly mercureUrlValue: string;
    private typewriterService: any;
    private eventSource!: EventSource;

    connect() {
        const conversationDiv: HTMLDivElement = document.querySelector('#conversation') as HTMLDivElement
        const userName: string = this.element.dataset?.userName ?? 'Un utilisateur'
        this.typewriterService = new TypewriterService(conversationDiv, userName)

        this.eventSource = new EventSource(this.mercureUrlValue, {
            withCredentials: true
        });

        this.listenToMercure()

        document.addEventListener('click', this.refreshListening)
    }

    refreshListening = () => {
        if (this.eventSource.readyState === EventSource.CLOSED) {
            this.listenToMercure()
        }
    }

    listenToMercure = () => {
        this.eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data)
            this.typewriterService.typewriter(data.mode)
        };

        this.eventSource.onerror = (error) => {
            console.error('Erreur avec Mercure')
        };
    }

    disconnect() {
        document.removeEventListener('click', this.refreshListening)
        this.eventSource?.close();
    }
}
