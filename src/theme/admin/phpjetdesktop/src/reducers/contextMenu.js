import {CLOSE_CONTEXT_MENU, OPEN_CONTEXT_MENU} from "../actions/contextMenu";

const initialState = {
    opened: false,
    mousePosition: {},
    children: []
};

const contextMenu = (state = initialState, action) => {
    switch (action.type) {
        case OPEN_CONTEXT_MENU:
            return {
                ...state,
                opened: true,
                children: action.children,
                mousePosition: action.mousePosition
            };

        case CLOSE_CONTEXT_MENU:
            return {
                ...state,
                opened: false
            };

        default:
            return state;
    }
};

export default contextMenu