import { useCallback } from 'react'
import { useDispatch } from 'react-redux'
import { AnyAction, ActionCreator } from '@reduxjs/toolkit'
import { AppThunk } from './reducer'

export const useAction = <T extends ActionCreator<AnyAction> | ((...args: any[]) => AppThunk)>(creator: T) => {
    const dispatch = useDispatch()

    return useCallback((...args: Parameters<T>) => { dispatch(creator(...args)) }, [dispatch, creator])
}
