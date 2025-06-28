import {MessageData} from "@/models/MessageDataInterface";

/**
 *
 * This class represent HTML for a message
 *
 */
export class Message {
    private message: { date: string; from: string; id: number | string; text: string; class: string };

    constructor(message: { date: string; from: string; id: number | string; text: string; class: string }) {
        this.message = message
    }

    getElement = () => {
        const container = document.createElement('div');
        const justify = this.message.class === 'from' ? 'justify-content-start' : 'justify-content-end'
        container.classList.add('d-flex', justify, 'align-items-start', 'gap-2')

        if(this.message.class === 'from') {
            container.appendChild(this.getAvatarElement())
        }
        container.appendChild(this.getMessageElement())
        return container
    }

    getMessageElement = () => {
        const messageElement = document.createElement('div');
        if (typeof this.message.id === "string") {
            messageElement.setAttribute('id', this.message.id)
        }
        messageElement.classList.add('message', this.message.class)
        messageElement.dataset.date = this.message.date
        messageElement.innerText = this.message.text
        return messageElement
    }

    getAvatarElement = () => {
        const avatarElement = document.createElement('div');
        avatarElement.classList.add('avatar')
        avatarElement.innerHTML = '<i class="fa-solid fa-user-astronaut"></i>'
        return avatarElement
    }
}
