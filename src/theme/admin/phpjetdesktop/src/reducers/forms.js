import {CREATE_FORM, SET_DISABLED_STATUS, SET_INPUT_VALUE} from "../actions/forms";

const initialState = {};

const forms = (state = initialState, action) => {
      switch (action.type) {
          case CREATE_FORM:
              return {
                  ...state,
                  [action.formID]: {
                      disabled: false,
                      values: {}
                  }
              };

          case SET_DISABLED_STATUS:
              return {
                  ...state,
                  [action.formID]: {
                      ...state[action.formID],
                      disabled: action.status
                  }
              };

          case SET_INPUT_VALUE:
              return {
                  ...state,
                  [action.formID]: {
                      ...state[action.formID],
                      values: {
                          ...state[action.formID].values,
                          [action.inputID]: action.value
                      }
                  }
              };

          default:
              return state;
      }
};

export default forms