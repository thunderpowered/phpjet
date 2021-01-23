import {SET_AUTHORIZED_STATUS} from "../actions/auth";

const initialState = {
    authorized: false,
    urls: {}
};

const auth = (state = initialState, action) => {
    switch (action.type) {
        case SET_AUTHORIZED_STATUS:
            return {
                ...state,
                authorized: action.authorized,
                urls: action.urls
            };
        default:
            return state;
    }
};

export default auth