import { Controller } from '@hotwired/stimulus';

import {ConversationService} from "@/service/ConversationService";
import {MessageData} from "@/models/MessageDataInterface";

/**
 * This controller aims to publish messages catching the submit form event
 */
export default class SenderController extends Controller<HTMLFormElement> {
    private conversationDiv!: HTMLDivElement;
    private submitter!: HTMLButtonElement;
    private textArea!: HTMLTextAreaElement;

    connect() {
        this.conversationDiv = document.querySelector('#conversation') as HTMLDivElement
        this.submitter = this.element.querySelector('button') as HTMLButtonElement
        this.textArea  = this.element.querySelector('textarea') as HTMLTextAreaElement
    }

    submit = (e: SubmitEvent) => {
        e.preventDefault()

        this.lockSubmitButton()

        const formData = new FormData(this.element)
        const formAction  = this.element.dataset.formAction

        if (!formAction) {
            console.warn('Form action is missing.')
            return;
        }

        if (this.textArea.value.trim() === '') return

        fetch(formAction, {
            method: 'POST',
            body: formData
        }).then(response => {

            if (response.status === 200) {
                return response.json()
            }
            return null

        }).then(data => {

            if (!data) return

            this.showMessage(data.message)
            this.prepareForNextMessage()
            this.unlockSubmitButton()

        }).catch(error => {
            console.error('Erreur lors de l’envoi du message :', error);
            this.prepareForNextMessage()
            this.unlockSubmitButton()
        });

    }

    lockSubmitButton = () => {
        this.submitter.setAttribute('disabled', 'disabled')
    }

    unlockSubmitButton = () => {
        this.submitter.removeAttribute('disabled')
    }

    showMessage = (message: MessageData) => {
        const conversationService = new ConversationService(this.conversationDiv)
        conversationService.appendMessage(message)
        conversationService.scrollToBottom()
    }

    prepareForNextMessage = () => {
        this.textArea.value = ''
        this.textArea.focus()
    }
}
