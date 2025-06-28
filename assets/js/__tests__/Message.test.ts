import { Message } from '@/Message';

describe('Message class', () => {
    test('getElement ajoute avatar et message avec classes "from"', () => {
        const data = {
            id: 'msg1',
            text: 'Hello world',
            from: 'Alice',
            date: '2025-07-09T12:00:00Z',
            class: 'from',
        };
        const message = new Message(data);
        const el = message.getElement();

        expect(el).toBeInstanceOf(HTMLDivElement);
        expect(el.classList.contains('d-flex')).toBe(true);
        expect(el.classList.contains('justify-content-start')).toBe(true);
        expect(el.classList.contains('align-items-start')).toBe(true);
        expect(el.classList.contains('gap-2')).toBe(true);

        const firstChild = el.children[0];
        expect(firstChild.classList.contains('avatar')).toBe(true);
        expect(firstChild.innerHTML).toContain('fa-user-astronaut');

        const messageEl = el.children[1] as HTMLDivElement;
        expect(messageEl.classList.contains('message')).toBe(true);
        expect(messageEl.classList.contains('from')).toBe(true);
        expect(messageEl.dataset.date).toBe(data.date);
        expect(messageEl.innerText).toBe(data.text);
        expect(messageEl.id).toBe(data.id);
    });

    test('getElement sans avatar quand class n\'est pas "from"', () => {
        const data = {
            id: 42,
            text: 'Reply message',
            from: 'Bob',
            date: '2025-07-09T12:05:00Z',
            class: 'to',
        };
        const message = new Message(data);
        const el = message.getElement();

        expect(el.classList.contains('justify-content-end')).toBe(true);

        expect(el.querySelector('.avatar')).toBeNull();

        const messageEl = el.children[0] as HTMLDivElement;
        expect(messageEl.classList.contains('message')).toBe(true);
        expect(messageEl.classList.contains('to')).toBe(true);
        expect(messageEl.dataset.date).toBe(data.date);
        expect(messageEl.innerText).toBe(data.text);
        expect(messageEl.id).toBe(''); // car id est number ici, pas string donc pas d'id attribué
    });
});
