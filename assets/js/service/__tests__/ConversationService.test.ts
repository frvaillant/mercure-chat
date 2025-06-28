import { ConversationService } from '@/service/ConversationService';
import { Message } from '@/Message';
import {MessageData} from "@/models/MessageDataInterface";

jest.mock('@/Message'); // mock la classe Message

describe('ConversationService', () => {
    let container: HTMLDivElement;
    let service: ConversationService;

    beforeEach(() => {

        container = document.createElement('div');
        Object.defineProperty(container, 'scrollHeight', { value: 1000, writable: true });
        container.scrollTop = 0;

        service = new ConversationService(container);

        (Message as jest.Mock).mockClear();
    });

    test('scrollToBottom sets scrollTop to scrollHeight', () => {
        service.scrollToBottom();
        expect(container.scrollTop).toBe(container.scrollHeight);
    });

    test('scrollToTop sets scrollTop to 0', () => {
        container.scrollTop = 100;
        service.scrollToTop();
        expect(container.scrollTop).toBe(0);
    });

    test('appendMessage creates a Message and appends its element', () => {
        const mockElement = document.createElement('div');
        (Message as jest.Mock).mockImplementation(() => ({
            getElement: () => mockElement,
        }));

        const msgData = { id: 1, text: 'hello', from: '2', date: 'now', class: 'from' } as MessageData;
        service.appendMessage(msgData);

        expect(Message).toHaveBeenCalledWith(msgData);
        expect(container.children.length).toBe(1);
        expect(container.children[0]).toBe(mockElement);
    });

    test('cleanSkeletons removes all skeleton elements', () => {

        const skeleton1 = document.createElement('div');
        skeleton1.className = 'message skeleton';
        const skeleton2 = document.createElement('div');
        skeleton2.className = 'message skeleton';
        const other = document.createElement('div');
        other.className = 'message';

        container.appendChild(skeleton1);
        container.appendChild(skeleton2);
        container.appendChild(other);

        service.cleanSkeletons();

        expect(container.querySelectorAll('.message.skeleton').length).toBe(0);
        expect(container.children.length).toBe(1);
        expect(container.children[0]).toBe(other);
    });

    test('buildConversationHtml appends messages, scrolls, and cleans skeletons', async () => {
        const messages = {
            1: { id: 1, text: 'msg1', from: 'a', date: 'd1', class: 'from' },
            2: { id: 2, text: 'msg2', from: 'b', date: 'd2', class: 'to' },
        };

        const mockElement1 = document.createElement('div');
        const mockElement2 = document.createElement('div');

        (Message as jest.Mock).mockImplementationOnce(() => ({
            getElement: () => mockElement1,
        })).mockImplementationOnce(() => ({
            getElement: () => mockElement2,
        }));

        const scrollSpy = jest.spyOn(service, 'scrollToBottom');
        const cleanSpy = jest.spyOn(service, 'cleanSkeletons');

        await service.buildConversationHtml(messages);

        expect(Message).toHaveBeenCalledTimes(2);
        expect(container.children.length).toBe(2);
        expect(container.children[0]).toBe(mockElement1);
        expect(container.children[1]).toBe(mockElement2);

        expect(scrollSpy).toHaveBeenCalledTimes(2);
        expect(cleanSpy).toHaveBeenCalled();
    });
});
