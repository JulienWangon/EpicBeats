import { configureStore} from '@reduxjs/toolkit';
import instrumentalReducer from '../features/instrumental/instrumentalSlice';

export const store = configureStore({
    reducer: {
        instrumental: instrumentalReducer,
    }
})