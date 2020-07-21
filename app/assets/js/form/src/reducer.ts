import { combineReducers, createReducer, createAction, Action } from '@reduxjs/toolkit'

import { ThunkAction } from 'redux-thunk'

import * as api from './api'
import { AppState, Method, InteractorI, Interactor, Protein, Mature, InteractorUI, Alignment, Sequences, Feedback } from './types'

/**
 * Regular actions.
 */
export const unselectMethod = createAction('UNSELECT_METHOD')
export const unselectProtein = createAction<{ i: InteractorI }>('UNSELECT_PROTEIN')
export const editMature = createAction<{ i: InteractorI }>('EDIT_MATURE')
export const updateMature = createAction<{ i: InteractorI, mature: Mature }>('UPDATE_MATURE')
export const showAlignment = createAction<{ i: InteractorI, alignment: Alignment }>('SHOW_ALIGNMENT')
export const addAlignment = createAction<{ i: InteractorI, alignment: Alignment }>('ADD_ALIGNMENT')
export const removeAlignment = createAction<{ i: InteractorI, index: number }>('REMOVE_ALIGNMENT')
export const cancelAlignment = createAction<{ i: InteractorI }>('CANCEL_ALIGNMENT')
export const resetForm = createAction('RESET_FORM')
export const showFeedback = createAction<Feedback>('SHOW_FEEDBACK')

/**
 * Private actions dispatched by thunks (end of the file).
 */
const __selectMethod = createAction<{ method: Method }>('SELECT_METHOD')
const __selectProtein = createAction<{ i: InteractorI, protein: Protein }>('SELECT_PROTEIN')
const __fireAlignment = createAction<{ i: InteractorI }>('FIRE_ALIGNMENT')
const __fireSave = createAction('FIRE_SAVE')

/**
 * Builds an interactor reducer (depends on i)
 */
const initialInteractorState: Interactor = {
    protein: {
        accession: null,
    },
    name: '',
    start: null,
    stop: null,
    mapping: [],
}

const buildInteractorReducer = (i: InteractorI) => createReducer<Interactor>(initialInteractorState, builder => {
    builder
        .addCase(__selectProtein, (state, action) => {
            if (i != action.payload.i) return state

            state.protein.accession = action.payload.protein.accession

            state.name = action.payload.protein.type == 'h'
                ? action.payload.protein.name
                : ''

            state.start = action.payload.protein.type == 'h' || action.payload.protein.matures.length == 0
                ? 1
                : null

            state.stop = action.payload.protein.type == 'h' || action.payload.protein.matures.length == 0
                ? action.payload.protein.sequence.length
                : null

            state.mapping = []
        })
        .addCase(unselectProtein, (state, action) => {
            if (i != action.payload.i) return state

            return initialInteractorState
        })
        .addCase(updateMature, (state, action) => {
            if (i != action.payload.i) return state

            state.name = action.payload.mature.name
            state.start = action.payload.mature.start
            state.stop = action.payload.mature.stop
            state.mapping = []
        })
        .addCase(addAlignment, (state, action) => {
            if (i != action.payload.i) return state

            state.mapping.push(action.payload.alignment)
        })
        .addCase(removeAlignment, (state, action) => {
            if (i != action.payload.i) return state

            state.mapping.filter((_, i) => i != action.payload.index)
        })
        .addCase(resetForm, () => initialInteractorState)
})

/**
 * Builds an interactor UI reducer (depends on i)
 */
const initialInteractorUiState: InteractorUI = {
    editing: false,
    processing: false,
    alignment: null,
}

const buildInteractorUIReducer = (i: InteractorI) => createReducer<InteractorUI>(initialInteractorUiState, builder => {
    builder
        .addCase(__selectProtein, (state, action) => {
            if (i != action.payload.i) return state

            state.editing = action.payload.protein.type == 'v'
        })
        .addCase(editMature, (state, action) => {
            if (i != action.payload.i) return state

            state.editing = true
        })
        .addCase(updateMature, (state, action) => {
            if (i != action.payload.i) return state

            state.editing = false
        })
        .addCase(__fireAlignment, (state, action) => {
            if (i != action.payload.i) return state

            state.processing = true
        })
        .addCase(showAlignment, (state, action) => {
            if (i != action.payload.i) return state

            state.alignment = action.payload.alignment
        })
        .addCase(addAlignment, (state, action) => {
            if (i != action.payload.i) return state

            state.processing = false
            state.alignment = null
        })
        .addCase(cancelAlignment, (state, action) => {
            if (i != action.payload.i) return state

            state.processing = false
            state.alignment = null
        })
        .addCase(resetForm, () => initialInteractorUiState)
})

/**
 * Build the actual reducer.
 */
const method = createReducer<{ psimi_id: string | null }>({ psimi_id: null }, build => {
    build
        .addCase(__selectMethod, (state, action) => {
            state.psimi_id = action.payload.method.psimi_id
        })
        .addCase(unselectMethod, (state) => {
            state.psimi_id = null
        })
        .addCase(resetForm, (state) => {
            state.psimi_id = null
        })
})

const saving = createReducer<boolean>(false, builder => {
    builder
        .addCase(__fireSave, () => true)
        .addCase(showFeedback, () => false)
})

const feedback = createReducer<Feedback | null>(null, builder => {
    builder
        .addCase(showFeedback, (_, action) => action.payload)
        .addDefaultCase(() => null)
})

const interactor1 = buildInteractorReducer(1);
const interactor2 = buildInteractorReducer(2);
const description = combineReducers({ method, interactor1, interactor2 })
const interactorUI1 = buildInteractorUIReducer(1);
const interactorUI2 = buildInteractorUIReducer(2);

export const reducer = combineReducers({
    description,
    interactorUI1,
    interactorUI2,
    saving,
    feedback,
})

/**
 * Thunk actions.
 */
type AppThunk = ThunkAction<void, AppState, unknown, Action<string>>

export const selectMethod = ({ psimi_id }: { psimi_id: string }): AppThunk => async dispatch => {
    // populate the cache.
    const f = () => {
        const method = api.methods.select(psimi_id).read()

        dispatch(__selectMethod({ method }))
    }

    try { f() } catch (promise) { promise.then(f) }
}

export const selectProtein = ({ i, accession }: { i: InteractorI, accession: string }): AppThunk => async dispatch => {
    // populate the cache.
    const f = () => {
        const protein = api.proteins.select(accession).read()

        dispatch(__selectProtein({ i, protein }))
    }

    try { f() } catch (promise) { promise.then(f) }
}

export const fireAlignment = ({ i, query, sequences }: { i: InteractorI, query: string, sequences: Sequences }): AppThunk => async dispatch => {
    dispatch(__fireAlignment({ i }))

    api.alignment(query, sequences)
        .then(alignment => dispatch(showAlignment({ i, alignment })))
}

export const fireSave = ({ run_id, pmid }: { run_id: number, pmid: number }): AppThunk => async (dispatch, getState) => {
    const state = getState()

    dispatch(__fireSave())

    api.save(run_id, pmid, state.description)
        .then(json => dispatch(showFeedback({ ...json })))
}
