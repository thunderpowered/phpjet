import {SET_AUTHORIZATION_ACTION, SET_AUTHORIZED_STATUS} from "../actions/auth";

const initialState = {
    authorized: undefined,
    admin_id: null,
    action: '1F'
};

const auth = (state = initialState, action) => {
    switch (action.type) {
        case SET_AUTHORIZED_STATUS:
            return {
                ...state,
                authorized: action.authorized,
                admin_id: action.admin_id
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