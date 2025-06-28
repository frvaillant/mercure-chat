import { TypewriterService } from '@/service/TypewriterService';

describe('TypewriterService', () => {
    let container: HTMLDivElement;
    let service: TypewriterService;

    beforeEach(() => {
        container = document.createElement('div');
        container.id = 'conversation';

        document.body.appendChild(container);

        service = new TypewriterService(container, 'Alice');

        jest.useFakeTimers();
    });

    afterEach(() => {
        document.body.innerHTML = '';
        jest.useRealTimers();
    });

    test('createTypingElement crée un div avec les classes et data-date', () => {
        const elem = service.createTypingElement();

        expect(elem).toBeInstanceOf(HTMLDivElement);
        expect(elem.classList.contains('message')).toBe(true);
        expect(elem.classList.contains('from')).toBe(true);
        expect(elem.classList.contains('pulse')).toBe(true);
        expect(elem.dataset.date).toBe('Alice écrit');
    });

    test('typewriter start ajoute l’élément et démarre l’intervalle', () => {
        service.typewriter('start');

        expect(container.contains(service['typingElement'])).toBe(true);

        expect(service['typingElement'].textContent).toBe('');

        jest.advanceTimersByTime(300);
        expect(service['typingElement'].textContent).toBe('.');

        jest.advanceTimersByTime(400);
        expect(service['typingElement'].textContent).toBe('..');

        jest.advanceTimersByTime(900);
        expect(service['typingElement'].textContent).toBe('...');

        expect(container.scrollTop).toBe(container.scrollHeight);
    });

    test('typewriter stop arrête l’intervalle et enlève l’élément', () => {
        service.typewriter('start');
        expect(container.contains(service['typingElement'])).toBe(true);

        service.typewriter('stop');

        expect(container.contains(service['typingElement'])).toBe(false);

        expect(service['typingElement'].textContent).toBe('');

        jest.advanceTimersByTime(1000);
        expect(service['typingElement'].textContent).toBe('');
    });

    test('typewriter stop sans start ne plante pas', () => {
        expect(service['typingInterval']).toBeNull();

        service.typewriter('stop');

        expect(container.contains(service['typingElement'])).toBe(false);
    });
});
