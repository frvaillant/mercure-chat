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
    private waintingContainer!: HTMLDivElement | null;
    private successContainer!: HTMLDivElement | null;
    private retryBtn!: HTMLLinkElement;

    connect() {

        this.eventSource = new EventSource(this.mercureUrlValue, {
            withCredentials: true
        });

        this.waintingContainer = document.querySelector('#waiting-mercure') as HTMLDivElement
        this.successContainer  = document.querySelector('#mercure-test') as HTMLDivElement
        this.retryBtn          = document.querySelector('#mercure-test-relaunch') as HTMLLinkElement

        this.retryBtn.addEventListener('click', this.relaunchTest)

        /**
         * After 2 seconds, we fetch a route which dispatch a mercure event
         * Every connected users will receive it
         */
        setTimeout(() => {
            void fetch('/test_mercure')
        }, 2000)

        this.listenToMercure()

    }

    listenToMercure = () => {
        this.eventSource.onmessage = (event) => {

            const data = JSON.parse(event.data)

            this.waintingContainer?.classList.add('d-none')
            this.successContainer?.classList.remove('d-none')

            if(!data.success) {
                this.displayErrorOrSuccess(false)
                return
            }
            this.displayErrorOrSuccess(true)

        };

        this.eventSource.onerror = (error) => {
            console.error('Erreur avec Mercure')
        };
    }

    displayErrorOrSuccess = (isSuccessful: boolean) => {
        const success = this.successContainer?.querySelector('#success')
        const error   = this.successContainer?.querySelector('#error')
        if(isSuccessful) {
            success?.classList.remove('d-none')
            error?.classList.add('d-none')
            return
        }
        success?.classList.add('d-none')
        error?.classList.remove('d-none')
    }

    relaunchTest = () => {
        this.waintingContainer?.classList.remove('d-none')
        this.successContainer?.classList.add('d-none')
        setTimeout(() => {
            void fetch('/test_mercure/1')
        }, 2000)
    }

    disconnect() {
        this.retryBtn.removeEventListener('click', this.relaunchTest)
        this.eventSource?.close();
    }
}
