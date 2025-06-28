/**
 * Model from front data from message
 */

export interface MessageData {
    id: number | string;
    text: string;
    from: string;
    date: string;
    class: string;
    fromName: string;
    toName: string;
}
