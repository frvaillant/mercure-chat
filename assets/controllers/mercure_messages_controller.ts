import { Controller } from '@hotwired/stimulus';

/**
 * This controller catches the typing mercure event and shows html while user is writing
 */
export default class MercureTypingController extends Controller<HTMLTextAreaElement> {

    static values = {
        mercureUrl: String,
    }
    declare readonly mercureUrlValue: string;
    private eventSource!: EventSource;

    connect() {

        this.eventSource = new EventSource(this.mercureUrlValue, {
            withCredentials: true
        });

        this.listenToMercure()
    }

    listenToMercure = () => {
        this.eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data)

            const notif = document.querySelector('#notif-' + data.from) as HTMLDivElement

            notif?.classList.remove('d-none')

            this.removeNotification(notif)

        };

        this.eventSource.onerror = (error) => {
            console.error('Erreur avec Mercure', error)
        };
    }

    removeNotification = (notif: HTMLDivElement) => {
        setTimeout(() => {
            notif?.classList.add('fadeout')
            setTimeout(() => {
                notif?.classList.add('d-none')
                notif?.classList.remove('fadeout')
            }, 1000)
        }, 5000)
    }

    disconnect() {
        this.eventSource?.close();
    }
}
