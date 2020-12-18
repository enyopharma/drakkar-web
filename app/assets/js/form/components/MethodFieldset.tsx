import React, { useRef, useCallback } from 'react'
import { methods as api } from '../src/api'
import { useAppSelector, useAction } from '../src/hooks'
import { selectMethod, unselectMethod } from '../src/reducer'
import { Resource, Method } from '../src/types'

import { SearchInput } from './Shared/SearchInput'

const useMethod = (): [Resource<Method> | null, (method_id: number) => void, () => void] => {
    const method_id = useAppSelector(state => state.method_id)

    const select = useAction(selectMethod)
    const unselect = useAction(unselectMethod)

    const resource = useRef<Resource<Method> | null>(null)

    resource.current = method_id === null ? null : api.select(method_id)

    const sselect = useCallback((method_id: number) => {
        resource.current = api.select(method_id)
        select({ id: method_id })
    }, [select])

    return [resource.current, sselect, unselect]
}

export const MethodFieldset: React.FC = () => {
    const [resource, select, unselect] = useMethod()

    return (
        <React.Suspense fallback={null}>
            <fieldset>
                <legend>Method</legend>
                <div className="row">
                    <div className="col">
                        {resource === null
                            ? <MethodSearchInput select={select} />
                            : <MethodAlert resource={resource} unselect={unselect} />}
                    </div>
                </div>
            </fieldset>
        </React.Suspense>
    )
}

type MethodSearchInputProps = {
    select: (method_id: number) => void
}

const MethodSearchInput: React.FC<MethodSearchInputProps> = ({ select }) => {
    const factory = useCallback((query: string) => api.search(query), [])

    return (
        <SearchInput
            type="method"
            factory={factory}
            select={select}
            placeholder="Search a method..."
            help="You may use + to perform queries with multiple search terms (eg: bio + tag)"
        />
    )
}

type MethodAlertProps = {
    resource: Resource<Method>
    unselect: () => void
}

const MethodAlert: React.FC<MethodAlertProps> = ({ resource, unselect }) => {
    const method = resource.read()

    return (
        <div className="alert alert-info">
            <strong>{method.psimi_id}</strong> - {method.name}
            <button type="button" className="close" onClick={() => unselect()}>
                <span>&times;</span>
            </button>
        </div>
    )
}
