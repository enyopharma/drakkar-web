import { useCallback } from 'react'
import { useSelector, useDispatch } from 'react-redux'
import { ActionCreator, AnyAction, ThunkAction, Action } from '@reduxjs/toolkit'
import { AppState, AppProps, InteractorProps, InteractorI, ProteinType } from './types'

export const useAppSelector = <T>(selector: (props: AppProps) => T): T => {
    return useSelector<AppState, T>(state => {
        const savable = !state.saving
            && !state.interactorUI1.editing && !state.interactorUI2.editing
            && !state.interactorUI1.processing && !state.interactorUI2.processing
            && state.description.method_id != null
            && state.description.interactor1.protein_id != null
            && state.description.interactor1.name != ''
            && state.description.interactor1.start != null
            && state.description.interactor1.stop != null
            && state.description.interactor2.protein_id != null
            && state.description.interactor2.name != ''
            && state.description.interactor2.start != null
            && state.description.interactor2.stop != null

        const resetable = !state.interactorUI1.processing && !state.interactorUI2.processing

        return selector({
            run_id: state.run_id,
            pmid: state.pmid,
            id: state.description.id,
            type: state.type,
            stable_id: state.description.stable_id,
            method_id: state.description.method_id,
            saving: state.saving,
            savable,
            resetable,
            feedback: state.feedback
        })
    })
}

export const useInteractorSelector = <T>(i: InteractorI, selector: (props: InteractorProps) => T): T => {
    return useSelector<AppState, T>(state => {
        const type1: ProteinType = 'h'
        const type2: ProteinType = state.type == 'hh' ? 'h' : 'v'
        const i1 = state.description.interactor1
        const i2 = state.description.interactor2
        const ui1 = state.interactorUI1
        const ui2 = state.interactorUI2

        const props = i == 1
            ? { i, type: type1, protein_id: i1.protein_id, name: i1.name, start: i1.start, stop: i1.stop, mapping: i1.mapping, ...ui1 }
            : { i, type: type2, protein_id: i2.protein_id, name: i2.name, start: i2.start, stop: i2.stop, mapping: i2.mapping, ...ui2 }

        return selector(props)
    })
}

type AppAction = ActionCreator<AnyAction | ThunkAction<void, AppState, unknown, Action<string>>>

type UseActionType = (creator: AppAction) => (...args: Parameters<AppAction>) => void

export const useAction: UseActionType = creator => {
    const dispatch = useDispatch()

    return useCallback((...args: Parameters<AppAction>) => { dispatch(creator(...args)) }, [dispatch, creator])
}
