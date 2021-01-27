import {SET_AUTHORIZATION_ACTION, SET_AUTHORIZED_STATUS} from "../actions/auth";

const initialState = {
    authorized: undefined,
    urls: {},
    action: '1F'
};

const auth = (state = initialState, action) => {
    switch (action.type) {
        case SET_AUTHORIZED_STATUS:
            return {
                ...state,
                authorized: action.authorized,
                urls: action.urls
            };

        case SET_AUTHORIZATION_ACTION:
            return {
                ...state,
                action: action.action // lol
            };

        default:
            return state;
    }
};

export default auth