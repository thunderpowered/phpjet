import {SET_DISABLED_STATUS} from "../actions/forms";

const initialState = {};

const forms = (state = initialState, action) => {
      switch (action.type) {
          case SET_DISABLED_STATUS:
              return {
                  ...state,
                  [action.formID]: {
                      ...state[action.formID],
                      disabled: action.status
                  }
              };
          default:
              return state;
      }
};

export default forms