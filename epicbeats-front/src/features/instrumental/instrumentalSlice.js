import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";

import { fetchInstrumentalsList } from "../../components/Instrumentals/instrumentalService";


//ACTION: RECUPERER LA LISTE DES INSTRUMENTALES
export const fetchInstrumentalsAsync = createAsyncThunk(
  'instrumentals/fetchInstrumentals',
  async () => {
      const response = await fetchInstrumentalsList();
      return response;
  }
);

//STATE INITIALE
const initialState = {
  list: [],
  isLoading: false,
  error: null,
};

export const instrumentalsSlice = createSlice({
  name: 'instrumentals',
  initialState,
  reducers: {},
  extraReducers: (builder) => {
      builder
          .addCase(fetchInstrumentalsAsync.pending, (state) => {
                state.isLoading = true;
          })

          .addCase(fetchInstrumentalsAsync.fulfilled, (state, action) => {
                state.isLoading = false;
                state.list = action.payload;
          })

          .addCase(fetchInstrumentalsAsync.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.error.message;
          })           
  },
});

export default instrumentalsSlice.reducer;
