import {AuthLoginStateInterface, ResponseMessagesInterface} from "./state";

export default {
    MESSAGES(state: AuthLoginStateInterface, item: ResponseMessagesInterface[]): void {
        state.messages = item;
    },
    USERNAME(state: AuthLoginStateInterface, item: string): void {
        state.username = item;
    },
    ACTIVE(state: AuthLoginStateInterface, item: boolean): void {
        state.active = item;
    },
}