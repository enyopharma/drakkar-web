export type ProteinType = 'h' | 'v'

export type Description = {
    id: number
    pmid: number
    run_id: number
    stable_id: string
    method: Method
    interactor1: Interactor
    interactor2: Interactor
    created_at: string
    deleted_at: string
    deleted: boolean
}

export type Method = {
    psimi_id: string
    name: string
}

export type Protein = {
    type: ProteinType
    accession: string
    sequence: string
    isoforms: Array<{ accession: string, sequence: string }>,
}

export type Interactor = {
    protein: {
        accession: string
    },
    name: string
    start: number
    stop: number
    mapping: Alignment[]
}

export type Alignment = {
    sequence: string
    isoforms: Isoform[]
}

export type Isoform = {
    accession: string
    occurrences: Array<{ start: number, stop: number }>
}
