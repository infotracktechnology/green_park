import { useState, useEffect, useCallback, useRef } from 'react';
import API from '../api/client';

export const useAnnouncementFilters = (initialData = {}) => {
  // ----- Master data -----
  const [master, setMaster] = useState(null);
  const [loading, setLoading] = useState(true);

  // ----- Filter values -----
  const [academicYear, setAcademicYear] = useState(initialData.academicYear || '');
  const [usertype, setUsertype] = useState(initialData.usertype || 'GROUP');
  const [course, setCourse] = useState(initialData.course || '');
  const [branches, setBranches] = useState(initialData.branches || []);
  const [coachingTypes, setCoachingTypes] = useState(initialData.coachingTypes || []);
  const [category, setCategory] = useState(initialData.category || []);
  const [batch, setBatch] = useState(initialData.batch || []);
  const [gender, setGender] = useState(initialData.gender || 'All');
  const [section, setSection] = useState(initialData.section || '');
  const [student, setStudent] = useState(initialData.student || '');

  // ----- Dynamic options -----
  const [sectionOptions, setSectionOptions] = useState([]);
  const [studentOptions, setStudentOptions] = useState({});
  const [studentLoading, setStudentLoading] = useState(false);
  const [studentSearch, setStudentSearch] = useState('');

  // ----- Visibility flags -----
  const [showGender, setShowGender] = useState(true);
  const [showSection, setShowSection] = useState(true);
  const [showStudent, setShowStudent] = useState(false);
  const [showCategory, setShowCategory] = useState(false);
  const [showBatch, setShowBatch] = useState(false);

  // Batch update helper for pre-filling announcement data cleanly
  const setAllFilters = useCallback((data) => {
    if (!data) return;
    if (data.academic_year) setAcademicYear(data.academic_year);
    if (data.usertype) setUsertype(data.usertype);
    if (data.course) setCourse(data.course);
    if (data.branch != null) {
      const bList = Array.isArray(data.branch)
        ? data.branch
        : String(data.branch)
            .split(',')
            .filter(Boolean)
            .map((b) => (isNaN(b) ? b : Number(b)));
      setBranches(bList);
    }
    if (data.coaching_type) {
      const cList = Array.isArray(data.coaching_type)
        ? data.coaching_type
        : String(data.coaching_type).split(',').filter(Boolean);
      setCoachingTypes(cList);
    }
    if (data.category) {
      const catList = Array.isArray(data.category)
        ? data.category
        : String(data.category).split(',').filter(Boolean);
      setCategory(catList);
    }
    if (data.batch) {
      const batList = Array.isArray(data.batch)
        ? data.batch
        : String(data.batch).split(',').filter(Boolean);
      setBatch(batList);
    }
    if (data.gender) setGender(data.gender);
    if (data.section) setSection(data.section);
    if (data.students) setStudent(String(data.students));
    if (data.studentOptions && typeof data.studentOptions === 'object') {
      setStudentOptions(data.studentOptions);
    }
  }, []);

  // ----- Fetch master data -----
  useEffect(() => {
    let isMounted = true;
    const fetchMaster = async () => {
      try {
        const res = await API.get('/admin/masterdata');
        if (isMounted && res.data?.status) {
          setMaster(res.data);
          if (!academicYear && res.data.academicyear?.academic_year) {
            setAcademicYear(res.data.academicyear.academic_year);
          }
        }
      } catch (e) {
        console.error('Master data error:', e);
      } finally {
        if (isMounted) setLoading(false);
      }
    };
    fetchMaster();
    return () => {
      isMounted = false;
    };
  }, []);

  // ----- Helper: toggle multi‑select chips -----
  const toggleSelection = useCallback((list, setList, val) => {
    if (!Array.isArray(list)) return;
    if (list.includes(val)) {
      setList(list.filter((x) => x !== val));
    } else {
      setList([...list, val]);
    }
  }, []);

  // ----- Fetch coaching types (depends on course + branches) -----
  const fetchCoachingTypes = useCallback(async () => {
    if (!course || !branches || branches.length === 0) return;
    try {
      const branchVal = Array.isArray(branches) ? branches.join(',') : String(branches);
      const params = {
        branch: branchVal,
        course,
      };
      const res = await API.get('/admin/filter', { params });
      if (res.data && Array.isArray(res.data)) {
        if (coachingTypes.length === 0) {
          setCoachingTypes(res.data);
        }
      }
    } catch (err) {
      console.error('Coaching types error:', err);
    }
  }, [course, branches, coachingTypes.length]);

  // ----- Fetch sections (GROUP or student filter) -----
  const fetchSections = useCallback(async () => {
    if (!course || !branches || branches.length === 0 || !coachingTypes || coachingTypes.length === 0) return;
    try {
      const params = {
        gender: gender || 'All',
        category: Array.isArray(category) ? category.join(',') : String(category || ''),
        batch: Array.isArray(batch) ? batch.join(',') : String(batch || ''),
        type: Array.isArray(coachingTypes) ? coachingTypes.join(',') : String(coachingTypes || ''),
        branch: Array.isArray(branches) ? branches.join(',') : String(branches || ''),
        course,
      };
      const res = await API.get('/admin/filter', { params });
      if (res.data && Array.isArray(res.data)) {
        setSectionOptions(res.data);
      }
    } catch (err) {
      console.error('Sections error:', err);
    }
  }, [course, branches, coachingTypes, category, batch, gender]);

  // ----- Fetch students (INDIVIDUAL) -----
  const fetchStudents = useCallback(async (customSearch) => {
    if (usertype !== 'INDIVIDUAL' || !course || !branches || branches.length === 0) return;
    setStudentLoading(true);
    try {
      const params = {
        get_students: 1,
        type: Array.isArray(coachingTypes) && coachingTypes.length ? coachingTypes.join(',') : '',
        branch: Array.isArray(branches) ? branches.join(',') : String(branches || ''),
        course,
        category: Array.isArray(category) ? category.join(',') : String(category || ''),
        batch: Array.isArray(batch) ? batch.join(',') : String(batch || ''),
        gender: gender !== 'All' ? gender : '',
        section: section || '',
        search: customSearch !== undefined ? customSearch : studentSearch,
      };
      const res = await API.get('/admin/filter', { params });
      if (res.data && typeof res.data === 'object' && !Array.isArray(res.data)) {
        setStudentOptions(res.data);
      } else if (Array.isArray(res.data)) {
        const mapped = {};
        res.data.forEach((s) => {
          if (s && s.student_id) mapped[s.student_id] = s.student_name || s.student_id;
        });
        setStudentOptions(mapped);
      } else {
        setStudentOptions({});
      }
    } catch (err) {
      console.error('Students error:', err);
    } finally {
      setStudentLoading(false);
    }
  }, [usertype, coachingTypes, branches, course, category, batch, gender, section, studentSearch]);

  // ----- Update visibility -----
  const updateVisibility = useCallback(() => {
    const isGroup = usertype === 'GROUP';
    const isIndividual = usertype === 'INDIVIDUAL';

    setShowGender(true);
    setShowSection(true);
    setShowStudent(isIndividual);

    const hasOffline = Array.isArray(coachingTypes) && coachingTypes.some((t) => typeof t === 'string' && t.includes('OFFLINE'));
    if (hasOffline && ['NEET', 'JEE'].includes(course)) {
      const branchIds = (Array.isArray(branches) ? branches : []).map((b) => Number(b));
      if (branchIds.some((id) => [1, 4, 5].includes(id))) {
        setShowCategory(true);
        setShowBatch(true);
      } else if (branchIds.some((id) => [3, 6].includes(id))) {
        setShowCategory(false);
        setShowBatch(true);
      } else {
        setShowCategory(false);
        setShowBatch(false);
      }
    } else {
      setShowCategory(false);
      setShowBatch(false);
    }
  }, [usertype, coachingTypes, course, branches]);

  // ----- Trigger fetches when dependencies change -----
  useEffect(() => {
    fetchCoachingTypes();
  }, [course, branches]);

  useEffect(() => {
    updateVisibility();
    fetchSections();
    if (usertype === 'INDIVIDUAL') {
      fetchStudents();
    }
  }, [usertype, course, branches, coachingTypes, category, batch, gender, section]);

  // ----- Exposed API -----
  return {
    loading,
    master,
    academicYear, setAcademicYear,
    usertype, setUsertype,
    course, setCourse,
    branches, setBranches,
    coachingTypes, setCoachingTypes,
    category, setCategory,
    batch, setBatch,
    gender, setGender,
    section, setSection,
    student, setStudent,
    sectionOptions,
    studentOptions, setStudentOptions,
    studentLoading,
    studentSearch, setStudentSearch,
    fetchStudents,
    showGender,
    showSection,
    showStudent,
    showCategory,
    showBatch,
    toggleSelection,
    setAllFilters,
  };
};