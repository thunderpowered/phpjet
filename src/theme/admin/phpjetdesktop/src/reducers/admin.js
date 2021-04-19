import {SET_ADMIN_SETTINGS} from "../actions/admin";

const initialState = {
    settings : {
        appearance: {}
    }
}

const admin = (state = initialState, action) => {
    switch (action.type) {
        case SET_ADMIN_SETTINGS:
            return {
                ...state,
                settings: {
                    ...state.settings,
                    [action.settings]: action.data
                }
            }
        default:
            return state;
    }
}

export default admin;