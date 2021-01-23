import {CHECK_AUTHORIZATION} from "../actions/auth";

const initialState = {
    authorized: false,
    urls: {}
};

const auth = (state = initialState, action) => {
    switch (action.type) {
        case CHECK_AUTHORIZATION:
            return {
                ...state,
                authorized: action.authorized,
                urls: action.urls
            };
        default:
            return state;
    }
};

export default auth;