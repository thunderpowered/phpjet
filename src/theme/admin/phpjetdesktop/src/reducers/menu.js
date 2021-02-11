import {SET_MENU_VISIBILITY, TOGGLE_MENU} from "../actions/menu";

const initialState = {
    opened: false
};

const menu = (state = initialState, action) => {
    switch (action.type) {
        case TOGGLE_MENU:
            return {
                opened: !state.opened
            };

        case SET_MENU_VISIBILITY:
            return {
                opened: action.opened
            };

        default:
            return state;
    }
};

export default menu