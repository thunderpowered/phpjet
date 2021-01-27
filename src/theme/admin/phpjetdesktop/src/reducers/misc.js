import {SET_MISC} from "../actions/misc";

const initialState = {
    misc: {
        logotype: '',
        version: ''
    }
};

const misc = (state = initialState, action) => {
    switch (action.type) {
        case SET_MISC:
            return action.misc;
        default:
            return state;
    }
};

export default misc