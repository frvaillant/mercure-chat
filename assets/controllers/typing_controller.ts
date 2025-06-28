import { Controller } from '@hotwired/stimulus';

/**
 * This controller detect if user is wrinting
 * If yes, it fetchs start or stop url to notify recipient via Mercure
 */
export default class TypingController extends Controller<HTMLTextAreaElement> {

    static values = {
        targetUser: String
    }

    declare readonly targetUserValue: string
    declare readonly hasTargetUserValue: boolean

    private hasBeenNotified: boolean = false
    private hasShiftPressed!: boolean
    private doneTypingInterval!: number;
    private typingTimer?: number;

    connect() {
        if (!this.hasTargetUserValue) {
            console.warn('[TypingController] Missing target user value');
            return;
        }

        this.hasShiftPressed = false
        this.doneTypingInterval = 2500;

        this.element.addEventListener('blur', this.onBlur)

        this.element.addEventListener('input', this.onInput);

        this.element.addEventListener('keydown', this.onKeyDown);

        /**
         * Allow sending message by pressing "Enter"
         */
        this.element.addEventListener('keyup', this.onKeyUp);

    }

    onBlur = () => {
        void this.notify('stop')
    }

    onInput = () => {

        if(this.element.value.trim().length === 0) {
            void this.notify('stop')
        }
        if(!this.hasBeenNotified && this.element.value.trim().length > 0) {
            void this.notify('start')
        }

        clearTimeout(this.typingTimer);

        this.typingTimer = window.setTimeout(() => {
            void this.notify('stop')

        }, this.doneTypingInterval);
    }

    onKeyDown = (e: KeyboardEvent) => {
        this.lockEnterKey(e)

        this.isShiftKeyPressed(e, true)
    }

    onKeyUp = (e: KeyboardEvent) => {
        this.isShiftKeyPressed(e, false)

        if(e.key === 'Enter' && !this.hasShiftPressed) {
            void this.notify('stop')
            const button = this.element.closest('form')?.querySelector('button[type="submit"]') as HTMLButtonElement
            button?.click()
        }
    }

    notify = async (state: 'start' | 'stop') => {
        await fetch(`/notify-typing/${this.targetUserValue}/${state}`, {
            method: 'POST'
        });
        this.hasBeenNotified = state === 'start';
    };

    /**
     * lock Enter key default behaviour if shift is pressed
     * @param e
     */
    lockEnterKey = (e: KeyboardEvent) => {
        if(e.key === 'Enter' && !this.hasShiftPressed) {
            e.preventDefault()
        }
    }

    /**
     * Store if shift key is pressed
     * @param e
     * @param isPressed
     */
    isShiftKeyPressed = (e: KeyboardEvent, isPressed: boolean) => {
        if(e.key === 'Shift') {
            this.hasShiftPressed = isPressed
        }
    }


    disconnect() {
        this.element.removeEventListener('input', this.onInput);
        this.element.removeEventListener('blur', this.onBlur);
        this.element.removeEventListener('keydown', this.onKeyDown);
        this.element.removeEventListener('keyup', this.onKeyUp);
        if (this.typingTimer) {
            clearTimeout(this.typingTimer);
        }
    }

}
