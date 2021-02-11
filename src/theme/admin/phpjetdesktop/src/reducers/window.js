import {OPEN_NEW_WINDOW} from "../actions/window";

const initialState = {
    list: [] // list of all windows, i decided to handle it by window reducer/action
};

const window = (state = initialState, action) => {
      switch (action.type) {
          case OPEN_NEW_WINDOW:
              return {
                  ...state,
                  list: [...state.list, action.window]
              };

          default:
              return state;
      }
};

export default window