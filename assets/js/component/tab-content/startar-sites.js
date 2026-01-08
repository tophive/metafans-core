import React, { useState, useEffect, useContext, createContext } from "react";
import { Grid, Star, LayoutGrid, Search, ChevronDown } from "lucide-react";
import {
  Importer,
  TABS,
  fetch_templates,
  fetch_categories,
} from "../../importer";

export const Context = createContext();

function StateProvider({ children }) {
  const [categories, set_categories] = useState([]);
  const [category, set_category] = useState("");
  const [plan, set_plan] = useState("");
  const [builder, set_builder] = useState("");
  const [search, set_search] = useState("");
  const [resource_type, set_resource_type] = useState(TABS.FULL_SITE);
  const [templates, set_templates] = useState([]);
  const [loading, set_loading] = useState("loading"); //"loading","success","error"

  useEffect(() => {
    fetch_templates({ resource_type, category, builder, plan, search }).then(
      (res) => {
        if (res !== false) {
          set_templates(res.data.templates);
          set_loading("success");
        } else {
          set_loading("error");
        }
      },
    );
  }, []);

  useEffect(() => {
    fetch_categories().then((res) => {
      if (res !== false) {
        set_categories(res.data);
      }
    });
  }, []);

  return (
    <Context.Provider
      value={{
        categories,
        set_categories,
        category,
        set_category,
        plan,
        set_plan,
        builder,
        set_builder,
        search,
        set_search,
        templates,
        set_templates,
        loading,
        set_loading,
        resource_type,
        set_resource_type,
      }}
    >
      {children}
    </Context.Provider>
  );
}

function FilterBar() {
  const [openFilter, setOpenFilter] = useState(null);
  const {
    categories,
    category,
    set_category,
    plan,
    set_plan,
    builder,
    set_builder,
    search,
    set_search,
    resource_type,
    set_templates,
    set_loading,
  } = useContext(Context);

  const [labels, set_labels] = useState([
    "All Categories",
    "All Plans",
    "All Builders",
  ]);

  const filters = [
    {
      icon: <Grid className="w-4 h-4" />,
      items: ["All Categories", ...categories],
      callback(cat) {
        set_category(cat);
      },
    },
    {
      icon: <Star className="w-4 h-4" />,
      items: ["All Plans", "Free", "Pro"],
      callback(plan) {
        set_plan(plan);
      },
    },
    {
      icon: <LayoutGrid className="w-4 h-4" />,
      items: ["All Builders", "Elementor", "Block", "Divi"],
      callback(builder) {
        set_builder(builder);
      },
    },
  ];

  const handleSubmit = async () => {
    set_loading("loading");

    try {
      fetch_templates({ resource_type, category, builder, plan, search }).then(
        (res) => {
          if (res !== false) {
            set_templates(res.data.templates);
            set_loading("success");
          } else {
            set_loading("error");
          }
        },
      );
    } catch (err) {}
  };

  return (
    <div className="w-[1140px] m-auto flex items-center justify-between border-b border-dashed border-gray-200 pb-4 relative">
      {/* Left side filters */}
      <div className="flex items-center gap-8">
        {filters.map((filter, idx) => (
          <div key={idx} className="relative">
            <button
              onClick={() => setOpenFilter(openFilter === idx ? null : idx)}
              className="flex items-center gap-2 text-gray-600 hover:text-gray-800 text-sm"
            >
              {filter.icon}
              <span>{labels[idx]}</span>
              <ChevronDown
                className={`w-4 h-4 transition-transform ${openFilter === idx ? "rotate-180" : ""}`}
              />
            </button>

            {/* Dropdown Menu */}
            {openFilter === idx && (
              <div className="absolute left-0 mt-2 w-40 bg-white shadow-md border rounded-md z-20">
                {filter.items.map((item, i) => (
                  <button
                    onClick={() => {
                      filter.callback(i == 0 ? "" : item);
                      set_labels((prev) => {
                        const updated = [...prev];
                        updated[idx] = item;
                        return updated;
                      });
                      setOpenFilter(null);
                    }}
                    key={i}
                    className="w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-800"
                  >
                    {item}
                  </button>
                ))}
              </div>
            )}
          </div>
        ))}
      </div>

      {/* Right side search + submit */}
      <div className="flex items-center gap-2 relative">
        <Search className="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
        <input
          type="text"
          placeholder="Search for a starter site..."
          className="!pl-9 !pr-4 !py-2 !border !rounded-md !text-sm !text-gray-600 focus:outline-none focus:ring-1 focus:ring-gray-300"
          value={search}
          onChange={(e) => set_search(e.target.value)}
        />
        <button
          onClick={handleSubmit}
          className="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700"
        >
          Submit
        </button>
      </div>
    </div>
  );
}

export default function Startar_Site() {
  return (
    <StateProvider>
      <FilterBar />
      <Importer />
    </StateProvider>
  );
}
