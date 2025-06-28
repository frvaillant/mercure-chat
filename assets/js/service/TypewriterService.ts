export class TypewriterService {
    private conversationContainer: HTMLDivElement;
    private targetUserName: string;
    private typingElement: HTMLDivElement;
    private typingInterval: null | number;

    /**
     *
     * this service build and shows "writing" html element
     *
     * The div which contains the conversation i.e div#conversation
     * @param conversationContainer
     * @param targetUserName
     */
    constructor(conversationContainer: HTMLDivElement, targetUserName: string) {
        this.conversationContainer = conversationContainer
        this.targetUserName = targetUserName
        this.typingElement = this.createTypingElement()
        this.typingInterval = null
    }

    createTypingElement = () => {
        const elem: HTMLDivElement = document.createElement('div')
        elem.classList.add('message', 'from', 'pulse')
        elem.dataset.date = `${this.targetUserName} écrit`
        return elem
    }

    typewriter = (mode: string) => {
        const conversationDiv: HTMLDivElement = document.querySelector('#conversation') as HTMLDivElement;
        const isStart: boolean = mode === 'start';
        const isStop: boolean  = mode === 'stop';

        if (!this.conversationContainer) return;

        if (isStart) {
            this.conversationContainer.appendChild(this.typingElement);

            const dots = ['.', '..', '...'];
            let index = 0;

            this.typingInterval = window.setInterval(() => {
                this.typingElement.textContent = dots[index];
                conversationDiv.scrollTop = conversationDiv.scrollHeight;
                index = (index + 1) % dots.length;

            }, 250) as unknown as number;
        }

        if (isStop) {
            if (this.typingInterval !== null) {
                clearInterval(this.typingInterval);
            }
            this.typingElement.textContent = '';

            if (this.conversationContainer.contains(this.typingElement)) {
                this.conversationContainer.removeChild(this.typingElement);
            }
        }
    }


}
