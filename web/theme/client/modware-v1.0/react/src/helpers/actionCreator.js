const DEFAULT_STATE = "SET_STATE";

export function createActionWrapper(type = DEFAULT_STATE) {

    return {
        type: type,
        state: {}
    }
}